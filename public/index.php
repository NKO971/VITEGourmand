<?php
require_once __DIR__ . '/../app/config/db.php';
// Initialiser la session et charger la BDD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/controllers/baseController.php';

$page = $_GET['page'] ?? 'home'; // Page par défaut : home
switch ($page) {
    // case 'mentions':
    //     require_once __DIR__ . '/../app/controllers/legal_controller.php';
    //     mentionsLegales($pdo);
    //     break;

    case 'home':
        require_once __DIR__ . '/../app/controllers/home_controller.php';
        homeController();
        break;

    case 'menus':
        require_once __DIR__ . '/../app/controllers/menus_controller.php';
        menusController();
        break;


    // case 'connexion':
    //     require_once __DIR__ . '/../app/controllers/auth_controller.php';
    //     loginController($pdo); 
    //     break;

    // case 'inscription':
    //     require_once __DIR__ . '/../app/controllers/auth_controller.php';
    //     registerController($pdo); 
    //     break;

    // case 'contact':
    //     require_once __DIR__ . '/../app/controllers/contact_controller.php';
    //     contactController($pdo);
    //     break;

    // case 'profile':
    //     require_once __DIR__ . '/../app/controllers/profile_controller.php';
    //     profileController($pdo);
    //     break;

    // case 'employee':
    //     require_once __DIR__ . '/../app/controllers/employee_controller.php';
    //     employeeController($pdo);
    //     break;

    // case 'admin':
    //     require_once __DIR__ . '/../app/controllers/admin_controller.php';
    //     adminController($pdo);
    //     break;

    default:
        // Page par défaut: redirection vers home
        header("Location: ?page=home");
        exit();
}
