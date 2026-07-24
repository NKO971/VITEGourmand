<?php
require_once __DIR__ . '/baseController.php';

function employeeController($pdo) {

    // Vérification de sécurité (Rôle Employé / Admin)
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        header('Location: ?page=home');
        exit();
    }

    // Préparation des variables pour la vue
    $data = [
        'pageTitle'  => 'Espace Employé - Gestion des Commandes',
    ];

    // Si tout est OK, on charge le tableau de bord
    BaseController::render(
        "Tableau de bord Employé - VITEGourmand",
        "employee_dashboard.view.php",
        [], // On utilisera le Bootstrap global du site pour l'instant
        ['js/dashboard-orders.js'],
        $data  
    );
}