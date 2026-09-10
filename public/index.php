<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Définir le chemin absolu de l'application
define('ROOT_PATH', dirname(__DIR__) . '/');

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger les variables d'environnement depuis le fichier .env
require_once ROOT_PATH . 'app/config/env.php';
loadEnv(ROOT_PATH . '.env');

// Chargement de la BDD et du contrôleur de base
require_once ROOT_PATH . 'app/config/db.php';
require_once ROOT_PATH . 'app/controllers/baseController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        require_once ROOT_PATH . 'app/controllers/homeController.php';
        homeController();
        break;

    case 'menus':
        require_once ROOT_PATH . 'app/controllers/menusController.php';
        menusController();
        break;

    case 'connexion':
        require_once ROOT_PATH . 'app/controllers/loginController.php';
        loginController($pdo); 
        break;

    case 'deconnexion':
        require_once ROOT_PATH . 'app/controllers/logoutController.php';
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
            require_once ROOT_PATH . 'app/models/AvisModel.php';
            
            $commandeId  = (int)($_POST['commande_id'] ?? 0);
            $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
            $userId      = $_SESSION['user_id'] ?? null;
            $nomClient   = $_SESSION['user_name'] ?? 'Client'; // Ajuster selon ta variable de session
            
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
        require_once ROOT_PATH . 'app/controllers/employeeController.php';
        employeeController($pdo);
        break;

    // Modération des avis ( Employé & Admin )
    case 'reviews':
    case 'employee_reviews': // Alias pour la compatibilité des liens
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/ReviewController.php';
        $reviewController = new ReviewController();
        $reviewController->index();
        break;

    case 'reviews_process':
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/ReviewController.php';
        $reviewController = new ReviewController();
        $reviewController->process();
        break;

    // Route AJAX pour valider ou refuser un avis (MongoDB)
    case 'update_avis_status':
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            exit();
        }
        
        require_once ROOT_PATH . 'app/models/AvisModel.php';
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $id = $data['id'] ?? null;
        $statut = $data['statut'] ?? null; // 'valide' ou 'refuse'
        
        if ($id && in_array($statut, ['valide', 'refuse'])) {
            $avisModel = new AvisModel();
            $updated = $avisModel->updateStatut($id, $statut);
            echo json_encode(['success' => $updated]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
        }
        exit();
        break;

    case 'get_orders':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCommandeController.php';
        getOrdersController($pdo);
        exit();
        break;
    
    case 'cancel_order' :
    case 'update_order_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCommandeController.php';
        updateOrderStatusController($pdo);
        exit();
        break;

    // Gestion de la carte (Employé & Admin)
    case 'employee_menus':
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        renderGestionCarteController($pdo);
        break;

    case 'toggle_menu_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        toggleMenuStatusController($pdo);
        exit();
        break;

    case 'create_plat':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        createPlatController($pdo);
        exit();
        break;

    case 'create_menu':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        createMenuController($pdo);
        exit();
        break;

    case 'update_menu':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        updateMenuController($pdo);
        exit();
        break;

    case 'toggle_plat_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        togglePlatStatusController($pdo);
        exit();
        break;

    case 'update_plat':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionCarteController.php';
        updatePlatController($pdo);
        exit();
        break;


    default:
        header("Location: ?page=home");
        exit();
}