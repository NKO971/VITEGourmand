<?php

function renderHorairesController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        header('Location: ?page=login');
        exit();
    }

    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);

    try {
        $horaires = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires : " . $e->getMessage());
        $horaires = [];
    }

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
        "Gestion des Horaires",
        "gestion_horaires.view.php",
        [],
        ['js/dashboard_horaires.js'],
        [
            'horaires'    => $horaires,
            'currentPage' => 'employee_schedules'
        ],
        'back'
    );
}

function updateHoraireController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès refusé.']);
        exit();
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true) ?? $_POST;

    $jour       = trim($data['jour'] ?? '');
    $ouverture  = trim($data['heure_ouverture'] ?? '');
    $fermeture  = trim($data['heure_fermeture'] ?? '');

    if (empty($jour) || empty($ouverture) || empty($fermeture)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Jour et horaires requis.']);
        exit();
    }

    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);

    try {
        $result = $horaireModel->updateHoraire($jour, $ouverture, $fermeture);

        if ($result) {
            echo json_encode(['success' => true, 'message' => "Horaires du {$jour} mis à jour."]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => "Aucune mise à jour effectuée pour {$jour}."]);
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO updateHoraire : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur BDD SQL.']);
    }
    exit();
}