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
        homeController($pdo);
        break;

    case 'menus':
        require_once ROOT_PATH . 'app/controllers/menusController.php';
        menusController($pdo);
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
        commandeController($pdo, $menuModel);
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

    // Gestion des commandes coté client (Modification, Annulation, Suivi)

    case 'profile':
        require_once ROOT_PATH . 'app/controllers/profileController.php';
        profileController($pdo);
        break;

    case 'modifier_commande':
        require_once ROOT_PATH . 'app/models/Menu.php';
        require_once ROOT_PATH . 'app/models/Commande.php';
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        $menuModel = new Menu($pdo);
        $commandeModel = new Commande($pdo);
        modifierCommandeController($pdo, $menuModel, $commandeModel);
        break;

    case 'update_commande':
        require_once ROOT_PATH . 'app/models/Menu.php';
        require_once ROOT_PATH . 'app/models/Commande.php';
        require_once ROOT_PATH . 'app/controllers/commandeController.php';
        $menuModel = new Menu($pdo);
        $commandeModel = new Commande($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                updateCommandeController($pdo, $menuModel, $commandeModel, $_POST);
            } catch (Exception $e) {
                echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
            }
        } else {
            header("Location: ?page=profile");
            exit();
        }
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
        require_once ROOT_PATH . 'app/controllers/avisController.php';
        traitementAvisController($pdo);
        break;

    // Gestion des employés (Admin uniquement)
    case 'admin_employes':
        require_once ROOT_PATH . 'app/controllers/adminController.php';
        renderAdminEmployesController($pdo);
        break;

    case 'create_employe':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/adminController.php';
        createEmployeController($pdo);
        exit();
        break;

    case 'toggle_user_active':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/adminController.php';
        toggleUserActiveController($pdo);
        exit();
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
        require_once ROOT_PATH . 'app/controllers/reviewController.php';
        $reviewController = new ReviewController();
        $reviewController->index();
        break;

    case 'reviews_process':
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/reviewController.php';
        $reviewController = new ReviewController();
        $reviewController->process();
        break;

    case 'employee_schedules':
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
            header('Location: ?page=connexion');
            exit();
        }
        require_once ROOT_PATH . 'app/controllers/gestionHoraireController.php';
        renderHorairesController($pdo);
        break;

    case 'update_horaire':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionHoraireController.php';
        updateHoraireController($pdo);
        exit();
        break;

    case 'admin_dashboard':
        require_once ROOT_PATH . 'app/controllers/adminDashboardController.php';
        renderAdminDashboardController($pdo);
        break;

    case 'get_chiffre_affaires':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/adminDashboardController.php';
        getChiffreAffairesController($pdo);
        exit();
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

    case 'cancel_order':
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
        require_once ROOT_PATH . 'app/controllers/gestionMenuController.php';
        renderGestionMenuController($pdo);
        break;

    case 'toggle_menu_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionMenuController.php';
        toggleMenuStatusController($pdo);
        exit();
        break;

    case 'create_plat':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionPlatController.php';
        createPlatController($pdo);
        exit();
        break;

    case 'create_menu':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionMenuController.php';
        createMenuController($pdo);
        exit();
        break;

    case 'update_menu':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionMenuController.php';
        updateMenuController($pdo);
        exit();
        break;

    case 'get_menu_gallery':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionMenuController.php';
        getMenuGalleryController($pdo);
        exit();
        break;

    case 'toggle_plat_status':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionPlatController.php';
        togglePlatStatusController($pdo);
        exit();
        break;

    case 'update_plat':
        header('Content-Type: application/json');
        require_once ROOT_PATH . 'app/controllers/gestionPlatController.php';
        updatePlatController($pdo);
        exit();
        break;

    // Gestion de la réinitialisation du mot de passe
    case 'oubli-mot-de-passe':
        require_once ROOT_PATH . 'app/controllers/passwordResetController.php';
        demandeResetController($pdo);
        break;

    case 'reset-mot-de-passe':
        require_once ROOT_PATH . 'app/controllers/passwordResetController.php';
        resetMotDePasseController($pdo);
        break;

    // Gestion du formulaire de contact
    case 'contact':
        require_once ROOT_PATH . 'app/controllers/contactController.php';
        contactController($pdo);
        break;

    // Gestion des pages légales
    case 'mentions-legales':
        require_once ROOT_PATH . 'app/controllers/legalController.php';
        mentionsLegalesController($pdo);
        break;

    case 'cgv':
        require_once ROOT_PATH . 'app/controllers/legalController.php';
        cgvController($pdo);
        break;

    default:
        header("Location: ?page=home");
        exit();
}
