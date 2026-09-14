<?php

function contactController($pdo)
{
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($titre) || empty($description) || empty($email)) {
            $error = 'Merci de remplir tous les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Le format de l'adresse email n'est pas valide.";
        } else {
            require_once ROOT_PATH . 'helpers/mailer.php';
            $mailSent = sendContactRequest($titre, $description, $email);

            if ($mailSent) {
                $success = "Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.";
            } else {
                $error = "Une erreur est survenue lors de l'envoi de votre message. Merci de réessayer plus tard.";
            }
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
        "Contact - VITEGourmand",
        "contact.view.php",
        [],
        [],
        [
            'error' => $error,
            'success' => $success,
            'horairesFooter' => $horairesFooter
        ]
    );
}