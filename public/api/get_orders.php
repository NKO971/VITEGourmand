<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé.']);
    exit();
}

require_once __DIR__ . '/../../app/config/db.php';

// Récupération des filtres depuis la requête GET
$search = !empty($_GET['search']) ? trim($_GET['search']) : null;
$status = !empty($_GET['status']) ? trim($_GET['status']) : null;
$date = !empty($_GET['date']) ? trim($_GET['date']) : null;

// Construction de la requête SQL avec des conditions dynamiques
$sql = "SELECT c.commande_id, c.date_commande, c.statut, u.nom, u.prenom
          FROM commande c
          JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
          WHERE 1=1"; // Condition toujours vraie pour faciliter l'ajout de conditions

// Ajout des conditions dynamiques en fonction des filtres
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
    $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR c.commande_id LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

// Ordre par date récente
$sql .= " ORDER BY c.date_commande DESC";

// Préparation et exécution de la requête
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retour des résultats au format JSON
    echo json_encode(['orders' => $orders]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des commandes.' . $e->getMessage()]);
    exit();
}