<?php
require_once ROOT_PATH . 'app/controllers/baseController.php';
require_once ROOT_PATH . 'app/models/AvisModel.php';

function employeeController($pdo) 
{
    // Sécurité (Rôle Employé / Admin)
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        header('Location: ?page=home');
        exit();
    }

    // Récupération des avis en attente depuis MongoDB
    $avisModel = new AvisModel();
    $avisEnAttente = $avisModel->getAvisByStatut('en_attente');

    // Transmission des données à la vue
    $data = [
        'pageTitle'     => 'Espace Employé - Gestion des Commandes et Modération',
        'avisEnAttente' => $avisEnAttente,
        'currentPage'   => 'employee'
    ];

    BaseController::render(
        "Tableau de bord Employé - VITEGourmand",
        "employee_dashboard.view.php",
        [],
        ['js/dashboard-orders.js', 'js/dashboard-avis.js'],
        $data,
        'back'
    );
}