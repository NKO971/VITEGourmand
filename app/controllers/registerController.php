<?php


function registerController($pdo)
{

    BaseController::render(
        "Créer un compte - VITEGourmand",
        "register.view.php",
        ["css/auth.css"],
        [],
        [
            'error' => $error ?? '',
            'success' => $success ?? ''
        ]
    );


    // On vérifie si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $gsm = trim($_POST['gsm'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Vérifications côté serveur
        if (empty($nom) || empty($prenom) || empty($email) || empty($gsm) || empty($adresse) || empty($password) || empty($password_confirm)) {
            $error = 'Vous avez oublié un champ.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Le format de l'adresse email n'est pas valide.";
        } elseif ($password !== $password_confirm) {
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
            require_once __DIR__ . '/../models/user.php';
            $userModel = new User($pdo);

            $result = $userModel->register($nom, $prenom, $email, $gsm, $adresse, $password);

            if ($result) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $_POST = [];
                header("refresh:2;url=index.php?page=connexion");
            } else {
        // Message générique uniquement, jamais l'erreur SQL réelle
                    $error = "Cette adresse email est déjà utilisée ou une erreur est survenue lors de l'inscription.";
                    }
        }
    }
    require_once __DIR__ . '/../views/register.view.php';
}
