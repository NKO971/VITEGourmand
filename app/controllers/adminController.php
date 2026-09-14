<?php

function renderAdminEmployesController($pdo)
{
    // Accès strictement réservé à l'admin (role_id = 1), pas aux employés (role_id = 2)
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1]);

    require_once ROOT_PATH . 'app/models/User.php';
    $userModel = new User($pdo);

    try {
        $employes = $userModel->getEmployes();
    } catch (PDOException $e) {
        error_log("Erreur chargement liste employés : " . $e->getMessage());
        $employes = [];
    }

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
        "Gestion des Employés",
        "gestion_employes.view.php",
        [],
        ['js/admin_employes.js'],
        [
            'employes'    => $employes,
            'currentPage' => 'admin_employes'
        ],
        'back'
    );
}

function createEmployeController($pdo)
{
    // Accès strictement réservé à l'admin (role_id = 1), pas aux employés (role_id = 2)
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1], true);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true) ?? $_POST;

    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Adresse email invalide.']);
        exit();
    }
        // Vérification de la complexité du mot de passe
    require_once ROOT_PATH . 'helpers/validators.php';
    $passwordError = validatePasswordStrength($password);
    if ($passwordError) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $passwordError]);
        exit();
    }

    require_once ROOT_PATH . 'app/models/User.php';
    $userModel = new User($pdo);

    $success = $userModel->createEmploye($email, $password);

    if ($success) {
        // Envoi du mail de notification (sans le mot de passe, conformément à la demande)
        require_once ROOT_PATH . 'helpers/mailer.php';
        $mailSent = sendEmployeeAccountCreatedNotification($email);
        error_log("Notification création employé à {$email} : " . ($mailSent ? "Succès" : "Échec"));

        echo json_encode(['success' => true, 'message' => 'Compte employé créé avec succès.']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cette adresse email est déjà utilisée ou une erreur est survenue.']);
    }
    exit();
}

function toggleUserActiveController($pdo)
{
    // Accès strictement réservé à l'admin (role_id = 1), pas aux employés (role_id = 2)
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1], true);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true) ?? [];

    $userId   = $data['user_id'] ?? null;
    $isActive = isset($data['is_active']) ? (int)$data['is_active'] : null;

    if (!$userId || !in_array($isActive, [0, 1], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Données invalides.']);
        exit();
    }

    require_once ROOT_PATH . 'app/models/User.php';
    $userModel = new User($pdo);

    try {
        $userModel->toggleActive($userId, $isActive);
        echo json_encode([
            'success' => true,
            'message' => $isActive === 1 ? 'Compte activé.' : 'Compte désactivé.'
        ]);
    } catch (PDOException $e) {
        error_log("Erreur toggleActive : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur base de données.']);
    }
    exit();
}
