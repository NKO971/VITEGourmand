<?php

function renderAdminDashboardController($pdo)
{
    // Accès strictement réservé à l'admin (role_id = 1), pas aux employés (role_id = 2)
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1]);

    require_once ROOT_PATH . 'app/models/StatsCommandeModel.php';
    require_once ROOT_PATH . 'app/models/Menu.php';
    $statsModel = new StatsCommandeModel();
    $menuModel  = new Menu($pdo);

    $commandesParMenu = $statsModel->getNombreCommandesParMenu();
    $annulationsMoisEnCours = $statsModel->getNombreAnnulationsMoisEnCours();

    // Résolution des titres de menus via le Model (pas de SQL en dur ici)
    $menuIds = array_column($commandesParMenu, 'menu_id');
    $menuTitres = $menuModel->getMenusByIds($menuIds);

    foreach ($commandesParMenu as &$item) {
        $item['titre'] = $menuTitres[$item['menu_id']] ?? "Menu #{$item['menu_id']}";
    }
    unset($item);

    $tousLesMenus = $menuModel->getAllMenusTitres();

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
        "Dashboard Administrateur",
        "admin_dashboard.view.php",
        [],
        [
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js',
            'js/admin_dashboard.js'
        ],
        [
            'commandesParMenu'       => $commandesParMenu,
            'tousLesMenus'           => $tousLesMenus,
            'annulationsMoisEnCours' => $annulationsMoisEnCours,
            'currentPage'            => 'admin_dashboard'
        ],
        'back'
    );
}

function getChiffreAffairesController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1], true);

    $menuId    = !empty($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;
    $dateDebut = !empty($_GET['date_debut']) ? trim($_GET['date_debut']) : null;
    $dateFin   = !empty($_GET['date_fin']) ? trim($_GET['date_fin']) : null;

    require_once ROOT_PATH . 'app/models/StatsCommandeModel.php';
    $statsModel = new StatsCommandeModel();

    $total = $statsModel->getChiffreAffaires($menuId, $dateDebut, $dateFin);

    echo json_encode([
        'success' => true,
        'total'   => $total
    ]);
    exit();
}
