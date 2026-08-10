<?php
session_start();
header('Content-Type: application/json');

// Contrôle d'accès (Employé = 2, Admin = 1)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé.']);
    exit();
}

require_once __DIR__ . '/../../app/config/db.php';

// Récupération des données JSON envoyées par le client
$data = json_decode(file_get_contents('php://input'), true);

$commandeId  = $data['commande_id'] ?? null;
$newStatus   = $data['statut'] ?? null;
$modeContact = $data['mode_contact'] ?? null;
$motif       = $data['motif_annulation'] ?? null;

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
if ($newStatus === 'Annulée') {
    if (empty($modeContact) || empty($motif)) {
        http_response_code(422);
        echo json_encode(['error' => 'Un mode de contact et un motif sont obligatoires pour annuler une commande.']);
        exit();
    }
}

try {
    // Transaction PDO : garantit que soit TOUT passe, soit RIEN ne passe en BDD
    $pdo->beginTransaction();

    // Récupération des infos de la commande et du client pour le mail
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

    // UPDATE de l'état courant dans la table commande
    $sql = "UPDATE commande 
            SET statut = :statut,
                mode_contact = :mode_contact,
                motif_annulation = :motif";

    // Si le statut passe à "Terminée" et qu'il y avait prêt de matériel, on clôture la restitution
    if ($newStatus === 'Terminée' && $order['pret_materiel'] == 1) {
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

    // Valider la transaction BDD
    $pdo->commit();

    // RÈGLE MÉTIER : Envoi du Mail de rappel si prêt de matériel
    $mailSent = false;
    if ($newStatus === 'En attente du retour de matériel') {
        $mailSent = sendEquipmentReturnEmail($order['email'], $order['prenom'], $order['numero_commande']);
    }

    echo json_encode([
        'success'  => true,
        'message'  => 'Statut mis à jour avec succès.',
        'mailSent' => $mailSent
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour en BDD : ' . $e->getMessage()]);
    exit();
}

/**
 * Fonction d'envoi d'e-mail pour la restitution du matériel (10 jours / 600€)
 */
function sendEquipmentReturnEmail($clientEmail, $clientName, $orderNum) {
    $subject = "Restitution du matériel prêté - Commande " . $orderNum;
    $message = "Bonjour " . htmlspecialchars($clientName) . ",\n\n"
             . "Votre commande (" . htmlspecialchars($orderNum) . ") a bien été livrée.\n"
             . "Conformément à nos conditions générales de vente, nous vous rappelons que le matériel prêté doit être restitué sous 10 jours ouvrés.\n"
             . "Passé ce délai, des frais de restitution d'un montant de 600 € vous seront facturés.\n\n"
             . "Merci de prendre contact avec notre société pour organiser le retour de ce matériel.\n\n"
             . "Cordialement,\nL'équipe VITEGourmand.";

    $headers = "From: contact@vitegourmand.fr\r\n" .
               "Reply-To: contact@vitegourmand.fr\r\n" .
               "Content-Type: text/plain; charset=UTF-8";

    return @mail($clientEmail, $subject, $message, $headers);
}