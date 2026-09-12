<?php
require_once ROOT_PATH . 'helpers/mailer.php';

// Contrôleur de gestion des statuts de commande (Back-office Employé / Admin)

function updateOrderStatusController($pdo)
{
    // Contrôle d'accès (Employé = 2, Admin = 1)
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

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

    // Liste exacte des statuts autorisés
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

    // RÈGLE MÉTIER : Annulation obligatoire avec mode de contact + motif
    if ($newStatus === 'Annulée' && (empty($modeContact) || empty($motif))) {
        http_response_code(422);
        echo json_encode(['error' => 'Un mode de contact et un motif sont obligatoires pour annuler une commande.']);
        exit();
    }

    try {
        // Transaction PDO : garantit l'intégrité des données
        $pdo->beginTransaction();

        // Récupération des infos de la commande et du client
        $stmtCheck = $pdo->prepare("
            SELECT c.*, u.email, u.prenom 
            FROM commande c
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.commande_id = :id
        ");
        $stmtCheck->execute([':id' => $commandeId]);
        $order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(['error' => 'Commande introuvable.']);
            exit();
        }

        // UPDATE de l'état courant
        $sql = "UPDATE commande 
                SET statut = :statut,
                    mode_contact = :mode_contact,
                    motif_annulation = :motif";

        if ($newStatus === 'Terminée' && isset($order['pret_materiel']) && $order['pret_materiel'] == 1) {
            $sql .= ", restitution_materiel = 1";
        }

        $sql .= " WHERE commande_id = :id";

        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute([
            ':statut'       => $newStatus,
            ':mode_contact' => ($newStatus === 'Annulée') ? $modeContact : null,
            ':motif'        => ($newStatus === 'Annulée') ? $motif : null,
            ':id'           => $commandeId
        ]);

        // INSERT dans l'historique suivi_commande
        $stmtSuivi = $pdo->prepare("
            INSERT INTO suivi_commande (commande_id, statut, date_modification, date_suivi) 
            VALUES (:commande_id, :statut, NOW(), NOW())
        ");
        $stmtSuivi->execute([
            ':commande_id' => $commandeId,
            ':statut'      => $newStatus
        ]);

        // Validation de la transaction
        $pdo->commit();

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

            // Appel du mail de retour matériel
            $mailSent = sendEquipmentReturnNotification(
                $order['email'],
                $order['prenom'],
                (string)$order['numero_commande'],
                $deadlineStr
            );
        } elseif ($newStatus === 'Annulée' && $modeContact === 'Mail') {
            // Appel du mail d'annulation
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
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la mise à jour en BDD : ' . $e->getMessage()]);
        exit();
    }
}

//Récupère la liste des commandes avec filtres pour le dashboard employé (AJAX)

function getOrdersController($pdo)
{
    // Contrôle d'accès (Employé = 2, Admin = 1)
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    // Récupération des filtres GET
    $search = !empty($_GET['search']) ? trim($_GET['search']) : null;
    $status = !empty($_GET['status']) ? trim($_GET['status']) : null;
    $date   = !empty($_GET['date'])   ? trim($_GET['date'])   : null;

    $sql = "SELECT 
                c.commande_id,
                c.numero_commande,
                c.date_commande,
                c.date_prestation,
                c.heure_livraison,
                c.prix_menu,
                c.nombre_personne,
                c.prix_livraison,
                (c.prix_menu + COALESCE(c.prix_livraison, 0)) AS montant_total,
                c.statut,
                c.pret_materiel,
                c.restitution_materiel,
                c.mode_contact,
                c.motif_annulation,
                u.nom,
                u.prenom,
                u.email
            FROM commande c
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE 1=1";

    $params = [];

    if ($status) {
        $sql .= " AND c.statut = :status";
        $params[':status'] = $status;
    }

    if ($date) {
        $sql .= " AND DATE(c.date_commande) = :date";
        $params[':date'] = $date;
    }

    if ($search) {
        $sql .= " AND (
            u.nom LIKE :search 
            OR u.prenom LIKE :search 
            OR c.commande_id LIKE :search 
            OR c.numero_commande LIKE :search
        )";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY c.date_commande DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['orders' => $orders]);
        exit();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la récupération des commandes.']);
        exit();
    }
}
