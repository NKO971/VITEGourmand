<?php
require_once ROOT_PATH . 'helpers/mailer.php';
require_once ROOT_PATH . 'app/models/Commande.php';

// Contrôleur de gestion des statuts de commande (Back-office Employé / Admin)

function updateOrderStatusController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);

    // Fonction utilitaire interne : Calcul de la date limite en jours ouvrés
    $calculateWorkingDaysDeadline = function (string $startDateStr, int $workingDays = 10): string {
        $date = new DateTime($startDateStr);
        $addedDays = 0;
        while ($addedDays < $workingDays) {
            $date->modify('+1 day');
            if ($date->format('N') < 6) { // Lundi à vendredi
                $addedDays++;
            }
        }
        return $date->format('d/m/Y');
    };

    // Récupération des données (compatible JSON Fetch + Formulaire HTML POST)
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    $commandeId = $data['commande_id'] ?? $_POST['commande_id'] ?? $_POST['cancel_commande_id'] ?? null;
    $newStatus = $data['statut'] ?? $_POST['statut'] ?? (($_GET['page'] ?? '') === 'cancel_order' ? 'Annulée' : null);
    $modeContact = $data['mode_contact'] ?? $_POST['mode_contact'] ?? null;
    $motif       = $data['motif_annulation'] ?? $_POST['motif_annulation'] ?? null;

    if (!$commandeId || !$newStatus) {
        http_response_code(400);
        echo json_encode(['error' => 'Données incomplètes.']);
        exit();
    }

    $allowedStatuses = [
        'En attente',
        'Acceptée',
        'En préparation',
        'En cours de livraison',
        'Livré',
        'En attente du retour de matériel',
        'Terminée',
        'Annulée'
    ];

    if (!in_array($newStatus, $allowedStatuses)) {
        http_response_code(400);
        echo json_encode(['error' => 'Statut invalide.']);
        exit();
    }

    if ($newStatus === 'Annulée' && (empty($modeContact) || empty($motif))) {
        http_response_code(422);
        echo json_encode(['error' => 'Un mode de contact et un motif sont obligatoires pour annuler une commande.']);
        exit();
    }

    $commandeModel = new Commande($pdo);

    $order = $commandeModel->getOrderWithUserInfo($commandeId);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Commande introuvable.']);
        exit();
    }

    $setRestitution = ($newStatus === 'Terminée' && isset($order['pret_materiel']) && $order['pret_materiel'] == 1);

    $success = $commandeModel->changeOrdersStatusWithFollowUp(
        $commandeId,
        $newStatus,
        ($newStatus === 'Annulée') ? $modeContact : null,
        ($newStatus === 'Annulée') ? $motif : null,
        $setRestitution
    );

    if (!$success) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la mise à jour en BDD.']);
        exit();
    }

    // Synchronisation MongoDB pour les statistiques (dashboard admin)
    require_once ROOT_PATH . 'app/models/StatsCommandeModel.php';
    $statsModel = new StatsCommandeModel();
    $montantTotal = (float)($order['prix_menu'] ?? 0) + (float)($order['prix_livraison'] ?? 0);
    $statsModel->upsertStats(
        (int)$commandeId,
        (int)($order['menu_id'] ?? 0),
        $montantTotal,
        $newStatus,
        $order['date_commande'] ?? date('Y-m-d')
    );

    // RÈGLE MÉTIER : Envoi du Mail de rappel si prêt de matériel via ton helper PHPMailer
    $mailSent = false;
    if ($newStatus === 'En attente du retour de matériel') {
        $refDate = $order['date_prestation'] ?? date('Y-m-d');
        $deadlineStr = $calculateWorkingDaysDeadline($refDate, 10);

        $mailSent = sendEquipmentReturnNotification(
            $order['email'],
            $order['prenom'],
            (string)$order['numero_commande'],
            $deadlineStr
        );
    } elseif ($newStatus === 'Annulée' && $modeContact === 'Mail') {
        $mailSent = sendOrderCancellationNotification(
            $order['email'],
            $order['prenom'],
            (string)$order['numero_commande'],
            $motif
        );
    }

    echo json_encode([
        'success'  => true,
        'message'  => 'Statut mis à jour avec succès.',
        'mailSent' => $mailSent
    ]);
    exit();
}

// Récupère la liste des commandes avec filtres pour le dashboard employé (AJAX)

function getOrdersController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);

    $search = !empty($_GET['search']) ? trim($_GET['search']) : null;
    $status = !empty($_GET['status']) ? trim($_GET['status']) : null;
    $date   = !empty($_GET['date'])   ? trim($_GET['date'])   : null;

    $commandeModel = new Commande($pdo);

    try {
        $orders = $commandeModel->searchOrders($search, $status, $date);
        echo json_encode(['orders' => $orders]);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la récupération des commandes.']);
        exit();
    }
}
