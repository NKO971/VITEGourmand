<?php

function demandeResetController($pdo)
{
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Merci de renseigner une adresse email valide.";
        } else {
            require_once __DIR__ . '/../models/User.php';
            require_once __DIR__ . '/../models/PasswordReset.php';
            $userModel = new User($pdo);
            $resetModel = new PasswordReset($pdo);

            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $token = $resetModel->createToken($user['utilisateur_id']);
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/VITEGourmand/public/?page=reset-mot-de-passe&token={$token}";

                require_once ROOT_PATH . 'helpers/mailer.php';
                sendPasswordResetEmail($email, $user['prenom'] ?? '', $resetLink);
            }

            // Message IDENTIQUE que l'email existe ou non : évite qu'un attaquant
            // puisse déterminer quels emails sont enregistrés (énumération de comptes)
            $success = "Si cette adresse est associée à un compte, un email de réinitialisation vient de vous être envoyé.";
        }
    }

    BaseController::render(
        "Mot de passe oublié - VITEGourmand",
        "oubli_mot_de_passe.view.php",
        ["css/auth.css"],
        [],
        [
            'error' => $error,
            'success' => $success
        ]
    );
}

function resetMotDePasseController($pdo)
{
    $error = '';
    $success = '';
    $token = $_GET['token'] ?? $_POST['token'] ?? '';

    require_once __DIR__ . '/../models/PasswordReset.php';
    $resetModel = new PasswordReset($pdo);

    $userId = $resetModel->verifyToken($token);

    if (!$userId) {
        BaseController::render(
            "Lien invalide - VITEGourmand",
            "reset_mot_de_passe_invalide.view.php",
            ["css/auth.css"],
            [],
            []
        );
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($password) || empty($passwordConfirm)) {
            $error = 'Vous avez oublié un champ.';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (strlen($password) < 10) {
            $error = 'Le mot de passe doit contenir au moins 10 caractères.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error = 'Le mot de passe doit contenir au moins une lettre majuscule.';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $error = 'Le mot de passe doit contenir au moins une lettre minuscule.';
        } elseif (!preg_match('/\d/', $password)) {
            $error = 'Le mot de passe doit contenir au moins un chiffre.';
        } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $error = 'Le mot de passe doit contenir au moins un caractère spécial.';
        } else {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($pdo);
            $userModel->updatePasswordById($userId, $password);

            // Usage unique : le token est détruit immédiatement après utilisation
            $resetModel->deleteToken($token);

            $success = "Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            header("refresh:2;url=index.php?page=connexion");
        }
    }

    BaseController::render(
        "Nouveau mot de passe - VITEGourmand",
        "reset_mot_de_passe.view.php",
        ["css/auth.css"],
        [],
        [
            'error' => $error,
            'success' => $success,
            'token' => $token
        ]
    );
}