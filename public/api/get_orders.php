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

// Récupération et nettoyage des filtres depuis la requête GET
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

// Filtre par statut (Match exact avec les valeurs BDD : "En attente", "Acceptée", etc.)
if ($status) {
    $sql .= " AND c.statut = :status";
    $params[':status'] = $status;
}

// Filtre par date
if ($date) {
    $sql .= " AND DATE(c.date_commande) = :date";
    $params[':date'] = $date;
}

// Recherche textuelle (Nom, Prénom, ID ou Référence N° commande)
if ($search) {
    $sql .= " AND (
        u.nom LIKE :search 
        OR u.prenom LIKE :search 
        OR c.commande_id LIKE :search 
        OR c.numero_commande LIKE :search
    )";
    $params[':search'] = '%' . $search . '%';
}

// Ordre d'affichage : les plus récents en premier
$sql .= " ORDER BY c.date_commande DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retour des résultats au format JSON
    echo json_encode(['orders' => $orders]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des commandes.']);
    exit();
}