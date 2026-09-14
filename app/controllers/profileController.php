<?php
require_once ROOT_PATH . 'app/models/User.php';
require_once ROOT_PATH . 'app/models/Commande.php';

function profileController($pdo)
{
    // Vérifie si l'utilisateur est connecté
    require_once ROOT_PATH . 'helpers/auth.php';
    requireLogin();

    $userModel = new User($pdo);
    $commandeModel = new Commande($pdo);

    $message = $_SESSION['flash_message'] ?? '';
    unset($_SESSION['flash_message']);
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $gsm = trim($_POST['gsm'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');

        if (!empty($nom) && !empty($prenom) && !empty($gsm) && !empty($adresse)) {
            if ($userModel->updateProfile($_SESSION['user_id'], $nom, $prenom, $gsm, $adresse)) {

                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['gsm'] = $gsm;
                $_SESSION['adresse'] = $adresse;

                $_SESSION['flash_message'] = "Profil mis à jour avec succès !";

                header("Location: ?page=profile");
                exit();
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        } else {
            $error = "Tous les champs sont obligatoires.";
        }
    }

    require_once ROOT_PATH . 'app/models/AvisModel.php';
    $avisModel = new AvisModel();

    $commandesAvecAvis = $avisModel->getCommandesIdsByUser($_SESSION['user_id']);

    $orders = $commandeModel->getOrdersByUserId($_SESSION['user_id']);

    $tousLesSuivis = [];
    foreach ($orders as $order) {
        if ($order['statut'] !== 'En attente' && $order['statut'] !== 'Annulée') {
            $tousLesSuivis[$order['commande_id']] = $commandeModel->getOrderFollowUp($order['commande_id']);
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
        "Mon Profil - VITEGourmand",
        "profile.view.php",
        ['css/profile.css'],
        [],
        [
            'message' => $message,
            'error' => $error,
            'orders' => $orders,
            'tousLesSuivis' => $tousLesSuivis,
            'commandesAvecAvis' => $commandesAvecAvis,
            'horairesFooter' => $horairesFooter
        ]
    );
}