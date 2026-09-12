<?php

function loginController($pdo) {
    $error = '';

    $specificCss = ['css/auth.css']; 
    $specificJS = ['js/connexion.js'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs.";
        } else {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($pdo);

            $user = $userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {

                // Blocage des comptes employé/admin désactivés
                // (n'affecte pas les clients : is_active n'est pas géré pour eux)
                if (in_array($user['role_id'], [1, 2]) && (int)($user['is_active'] ?? 0) !== 1) {
                    $error = "Ce compte a été désactivé. Merci de contacter votre administrateur.";
                } else {

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['user_id'] = $user['utilisateur_id'];
                    $_SESSION['nom'] = $user['nom'];
                    $_SESSION['prenom'] = $user['prenom'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['gsm'] = $user['gsm'];

                    if ($_SESSION['role_id'] == 3) {
                        header("Location: ?page=profile");
                        exit();
                    } elseif ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 1) {
                        header("Location: ?page=employee");
                        exit();
                    } else {
                        header("Location: ?page=home");
                        exit();
                    }
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
    }

    BaseController::render(
        "Connexion - VITEGourmand",
        "connexion.view.php",
        $specificCss,
        $specificJS,
        [
            'error' => $error ?? ''
        ]
    );
}