<?php
function menusController() { 
    global $pdo;

    $stmtTheme = $pdo->query("SELECT * FROM " . TABLE_THEME);
    $themes = $stmtTheme->fetchAll();

    $regimes = $pdo->query("SELECT * FROM " . TABLE_REGIME)->fetchAll();
    
    $menus = $pdo->query("SELECT * FROM " . TABLE_MENU)->fetchAll();

    require_once(__DIR__ . '/baseController.php');

    BaseController::render( 
        "Nos Menus",
        "menus.view.php",
        ["css/page-menu.css"],
        ["js/menus-filter.js"],
        [
            'themes' => $themes,
            'regimes' => $regimes,
            'menus' => $menus
        ]
    );       
}