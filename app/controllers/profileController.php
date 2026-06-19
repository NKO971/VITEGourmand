<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/Commande.php';

function profileController($pdo)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Protection : si l'utilisateur n'est pas connecté, retour à la connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?page=connexion");
        exit();
    }

    $userModel = new User($pdo);
    $commandeModel = new Commande($pdo);

    $message = $_SESSION['flash_message'] ?? '';
    unset($_SESSION['flash_message']);
    $error = '';

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $gsm = trim($_POST['gsm'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');

        if (!empty($nom) && !empty($prenom) && !empty($gsm) && !empty($adresse)) {
            if ($userModel->updateProfile($_SESSION['user_id'], $nom, $prenom, $gsm, $adresse)) {

                // Mise à jour de la session
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['gsm'] = $gsm;
                $_SESSION['adresse'] = $adresse;

                $_SESSION['flash_message'] = "Profil mis à jour avec succès !";

                // REDIRECTION vers la même page pour éviter le re-post du formulaire
                header("Location: ?page=profile");
                exit();
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        } else {
            $error = "Tous les champs sont obligatoires.";
        }
    }

    // Récupération des commandes de l'utilisateur
    $orders = $commandeModel->getOrdersByUserId($_SESSION['user_id']);

    $tousLesSuivis = [];
    foreach ($orders as $order) {
        // On récupère le suivi seulement si la commande n'est pas "En attente" ou "Annulée"
        if ($order['statut'] !== 'En attente' && $order['statut'] !== 'Annulée') {
            $tousLesSuivis[$order['commande_id']] = $commandeModel->getOrderFollowUp($order['commande_id']);
        }
    }

    BaseController::render(
        "Mon Profil - VITEGourmand",
        "profile.view.php",
        ['css/profile.css'],
        [],
        [
            'message' => $message,
            'error' => $error,
            'orders' => $orders,
            'tousLesSuivis' => $tousLesSuivis 
        ]
    );
}
