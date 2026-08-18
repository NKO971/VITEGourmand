<?php
// Définir le chemin absolu de l'application
define('ROOT_PATH', dirname(__DIR__) . '/');

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chargement de la BDD et du contrôleur de base
require_once ROOT_PATH . 'app/config/db.php';
require_once ROOT_PATH . 'app/controllers/baseController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        require_once ROOT_PATH . 'app/controllers/home_controller.php';
        homeController();
        break;

    case 'menus':
        require_once ROOT_PATH . 'app/controllers/menus_controller.php';
        menusController();
        break;

    case 'connexion':
        require_once ROOT_PATH . 'app/controllers/loginController.php';
        loginController($pdo); 
        break;

    case 'deconnexion':
        require_once ROOT_PATH . 'app/controllers/logout_controller.php';
        break;

    case 'inscription':
        require_once ROOT_PATH . 'app/controllers/registerController.php';
        registerController($pdo); 
        break;
        
    case 'commander':
        require_once ROOT_PATH . 'app/models/Menu.php';
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        $menuModel = new Menu($pdo);
        commandeController($menuModel);
        break;
    
    case 'api_zone':
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        getZoneDistance($pdo);
        exit();
        break;
    
    case 'enregistrer_commande':
        require_once ROOT_PATH . 'app/models/Menu.php';
        require_once ROOT_PATH . 'app/models/Commande.php';
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        
        $menuModel = new Menu($pdo); 
        $commandeModel = new Commande($pdo);      
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                enregistrerCommande($pdo, $menuModel, $commandeModel, $_POST);
            } catch (Exception $e) {
                echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
            }
        } else {
            header("Location: ?page=home");
            exit();
        }
        break;

    case 'confirmation':
        BaseController::render(
            "Confirmation - VITEGourmand",
            "confirmation.view.php",
            [],
            [], 
            []  
        );
        break;
    
    case 'profile':
        require_once ROOT_PATH . 'app/controllers/profileController.php';
        profileController($pdo);
        break;
        
    case 'annuler':
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        annulerCommandeController($pdo);
        break;

    case 'donner_avis':
        BaseController::render(
            "Donner un avis - VITEGourmand",
            "donner_avis.view.php",
            [],
            [], 
            []  
        );
        break;

    case 'traitement_avis':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once ROOT_PATH . 'app/config/mongo.php';
            
            $commandeId  = $_POST['commande_id'] ?? '';
            $commentaire = isset($_POST['commentaire']) ? trim(htmlspecialchars($_POST['commentaire'])) : '';
            $userId      = $_SESSION['user_id'] ?? null;
            
            $hasNote = isset($_POST['note']) && $_POST['note'] !== '';
            $note    = $hasNote ? (int) $_POST['note'] : null;
            
            if ($commandeId && $userId && $note !== null && $note >= 1 && $note <= 5 && !empty($commentaire)) {
                try {
                    $collection = $db->avis;
                    $collection->insertOne([
                        'commande_id' => $commandeId,
                        'user_id'     => $userId,
                        'note'        => $note,
                        'commentaire' => $commentaire,
                        'date'        => new MongoDB\BSON\UTCDateTime()
                    ]);
                    
                    header('Location: ?page=profile');
                    exit();
                    
                } catch (Exception $e) {
                    die("Erreur MongoDB : " . $e->getMessage());
                }
            } else {
                die("Erreur : Données manquantes, note invalide (1 à 5) ou utilisateur non connecté.");
            }
        } else {
            header("Location: ?page=profile");
            exit();
        }
        break;

    // Dashboard Employé & Admin
    case 'employee':
    case 'employee_dashboard':
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/employee_controller.php';
        employeeController($pdo);
        break;

    case 'get_orders':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCommandeController.php';
        getOrdersController($pdo);
        exit();
        break;
    
    case 'update_order_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/employeCommandeController.php';
        updateOrderStatusController($pdo);
        exit();
        break;

    default:
        header("Location: ?page=home");
        exit();
}