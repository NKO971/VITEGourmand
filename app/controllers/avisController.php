<?php

function traitementAvisController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ?page=profile");
        exit();
    }

    require_once ROOT_PATH . 'app/models/AvisModel.php';

    $commandeId  = (int)($_POST['commande_id'] ?? 0);
    $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
    $userId      = $_SESSION['user_id'];
    $nomClient   = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?: 'Client';

    $hasNote = isset($_POST['note']) && $_POST['note'] !== '';
    $note    = $hasNote ? (int) $_POST['note'] : null;

    if ($commandeId && $userId && $note !== null && $note >= 1 && $note <= 5 && !empty($commentaire)) {
        $avisModel = new AvisModel();
        $success = $avisModel->createAvis($userId, $nomClient, $note, $commentaire, $commandeId);

        if ($success) {
            header('Location: ?page=profile');
            exit();
        } else {
            die("Erreur lors de l'enregistrement de l'avis.");
        }
    } else {
        die("Erreur : Données manquantes ou note invalide (1 à 5).");
    }
}