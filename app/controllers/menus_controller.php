<?php
function menusController() { 
    global $pdo;

    $stmtTheme = $pdo->query("SELECT * FROM " . TABLE_THEME);
    $themes = $stmtTheme->fetchAll();

    $regimes = $pdo->query("SELECT * FROM " . TABLE_REGIME)->fetchAll();
    
    $menusQuery = "select
        m.*,
        t.libelle as theme_libelle,
        r.libelle as regime_libelle
    from " . TABLE_MENU . " m
    left join " . TABLE_THEME . " t on m.theme_id = t.theme_id
    left join " . TABLE_REGIME . " r on m.regime_id = r.regime_id";
    $menus = $pdo->query($menusQuery)->fetchAll();

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