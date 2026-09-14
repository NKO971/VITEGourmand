<?php

function registerController($pdo)
{
    $error = '';
    $success = '';

    // Inclure le fichier validators.php pour utiliser la fonction validatePasswordStrength
    require_once ROOT_PATH . 'helpers/validators.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $gsm = trim($_POST['gsm'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($gsm) || empty($adresse) || empty($password) || empty($password_confirm)) {
            $error = 'Vous avez oublié un champ.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Le format de l'adresse email n'est pas valide.";
        } elseif ($password !== $password_confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif ($passwordError = validatePasswordStrength($password)) {
            $error = $passwordError;
        } else {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($pdo);

            $result = $userModel->register($nom, $prenom, $email, $gsm, $adresse, $password);

            if ($result) {
                require_once ROOT_PATH . 'helpers/mailer.php';
                sendWelcomeEmail($email, $prenom);

                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $_POST = [];
                header("refresh:2;url=index.php?page=connexion");
            } else {
                $error = "Cette adresse email est déjà utilisée ou une erreur est survenue lors de l'inscription.";
            }
        }
    }

    BaseController::render(
        "Créer un compte - VITEGourmand",
        "register.view.php",
        ["css/auth.css"],
        [],
        [
            'error' => $error,
            'success' => $success
        ]
    );
}
