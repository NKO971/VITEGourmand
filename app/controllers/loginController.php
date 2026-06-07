<?php

function loginController($pdo) {
    $error = '';

    $specificCss = ['css/auth.css']; 
    $specificJS = ['public/js/connexion.js'];

    // On vérifie si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs.";
        } else {
            // On charge le modèle User
            require_once __DIR__ . '/../models/user.php';
            $userModel = new User($pdo);

            // On cherche l'utilisateur par son email
            $user = $userModel->getUserByEmail($email);

            // Si l'utilisateur existe, on vérifie son mot de passe haché
            if ($user && password_verify($password, $user['password'])) {
                
                // Authentification réussie : On initialise la session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $user['utilisateur_id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role_id'] = $user['role_id'];

                header("Location: index.php?page=menus");
                exit();
            } else {
                $error = "Identifiants incorrects.";
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

    // On charge la vue
    require_once __DIR__ . '/../views/connexion.view.php';
}