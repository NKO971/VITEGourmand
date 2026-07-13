<?php
require_once __DIR__ . '/baseController.php';

function employeeController($pdo)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Protection : Si pas connecté, redirection vers la page de connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?page=connexion");
        exit();
    }

    if ($_SESSION['role_id'] == 3) {
        header("Location: ?page=profile");
        exit();
    }

    // 3. Si tout est OK, on charge le tableau de bord
    BaseController::render(
        "Tableau de bord Employé - VITEGourmand",
        "employee_dashboard.view.php",
        [], // On utilisera le Bootstrap global du site pour l'instant
        [], 
        []  
    );
}