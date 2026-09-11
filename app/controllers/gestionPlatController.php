<?php

function createPlatController(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée.']);
        return;
    }

    $titrePlat = isset($_POST['titre_plat']) ? trim(strip_tags($_POST['titre_plat'])) : '';
    $actif = isset($_POST['actif']) ? (int)$_POST['actif'] : 1;

    if (empty($titrePlat)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Le titre du plat est obligatoire.']);
        return;
    }

    require_once ROOT_PATH . 'app/models/Plat.php';
    $platModel = new Plat($pdo);

    try {
        $platId = $platModel->createPlat($titrePlat, $actif);

        echo json_encode([
            'success' => true,
            'message' => 'Plat créé avec succès.',
            'plat_id' => $platId
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du plat en base de données.']);
    }
}

function updatePlatController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    $platId    = $_POST['plat_id'] ?? null;
    $titrePlat = trim(strip_tags($_POST['titre_plat'] ?? ''));

    if (!$platId || empty($titrePlat)) {
        http_response_code(400);
        echo json_encode(['error' => 'Le titre du plat est obligatoire.']);
        exit();
    }

    $photoData = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileMime     = mime_content_type($_FILES['photo']['tmp_name']);

        if (!in_array($fileMime, $allowedMimes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format d\'image invalide (JPG, PNG, WEBP uniquement).']);
            exit();
        }

        $photoData = file_get_contents($_FILES['photo']['tmp_name']);
    }

    require_once ROOT_PATH . 'app/models/Plat.php';
    $platModel = new Plat($pdo);

    try {
        $platModel->updatePlat($platId, $titrePlat, $photoData);

        echo json_encode(['success' => true, 'message' => 'Plat mis à jour avec succès.']);
        exit();

    } catch (PDOException $e) {
        error_log("Erreur updatePlat : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}

function togglePlatStatusController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $platId = $data['plat_id'] ?? null;
    $actif  = isset($data['actif']) ? (int)$data['actif'] : null;

    if (!$platId || !in_array($actif, [0, 1], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides.']);
        exit();
    }

    require_once ROOT_PATH . 'app/models/Plat.php';
    $platModel = new Plat($pdo);

    try {
        $platModel->toggleStatus($platId, $actif);

        echo json_encode([
            'success' => true,
            'message' => $actif === 1 ? 'Plat réactivé.' : 'Plat masqué (archivé).'
        ]);
        exit();

    } catch (PDOException $e) {
        error_log("Erreur togglePlatStatus : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}