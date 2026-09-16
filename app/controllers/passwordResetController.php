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
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "<?= BASE_URL ?>/?page=reset-mot-de-passe&token={$token}";

                require_once ROOT_PATH . 'helpers/mailer.php';
                sendPasswordResetEmail($email, $user['prenom'] ?? '', $resetLink);
            }

            $success = "Si cette adresse est associée à un compte, un email de réinitialisation vient de vous être envoyé.";
        }
    }

    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    BaseController::render(
        "Mot de passe oublié - VITEGourmand",
        "oubli_mot_de_passe.view.php",
        ["css/auth.css"],
        [],
        [
            'error' => $error,
            'success' => $success,
            'horairesFooter' => $horairesFooter
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

    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    if (!$userId) {
        BaseController::render(
            "Lien invalide - VITEGourmand",
            "reset_mot_de_passe_invalide.view.php",
            ["css/auth.css"],
            [],
            [
                'horairesFooter' => $horairesFooter
            ]
        );
        return;
    }

    require_once ROOT_PATH . 'helpers/validators.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($password) || empty($passwordConfirm)) {
            $error = 'Vous avez oublié un champ.';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif ($passwordError = validatePasswordStrength($password)) {
            $error = $passwordError;
        } else {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($pdo);
            $userModel->updatePasswordById($userId, $password);

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
            'token' => $token,
            'horairesFooter' => $horairesFooter
        ]
    );
}
