<?php

function renderAdminEmployesController($pdo) {
    // Accès strictement réservé à l'admin (role_id = 1), pas aux employés
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
        header('Location: ?page=connexion');
        exit();
    }

    require_once ROOT_PATH . 'app/models/User.php';
    $userModel = new User($pdo);

    try {
        $stmt = $pdo->query("SELECT utilisateur_id, email, is_active FROM utilisateur WHERE role_id = 2 ORDER BY utilisateur_id DESC");
        $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function createEmployeController($pdo) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès réservé à l\'administrateur.']);
        exit();
    }

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
    if (strlen($password) < 10
        || !preg_match('/[A-Z]/', $password)
        || !preg_match('/[a-z]/', $password)
        || !preg_match('/\d/', $password)
        || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.']);
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

function toggleUserActiveController($pdo) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès réservé à l\'administrateur.']);
        exit();
    }

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

