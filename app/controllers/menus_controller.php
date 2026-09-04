<?php
function menusController()
{
    global $pdo;

    $stmtTheme = $pdo->query("SELECT * FROM " . TABLE_THEME);
    $themes = $stmtTheme->fetchAll();

    $regimes = $pdo->query("SELECT * FROM " . TABLE_REGIME)->fetchAll();

    $menusQuery = "SELECT
    m.*,
    t.libelle AS theme_libelle,
    r.libelle AS regime_libelle
    FROM " . TABLE_MENU . " m
    LEFT JOIN " . TABLE_THEME . " t ON m.theme_id = t.theme_id
    LEFT JOIN " . TABLE_REGIME . " r ON m.regime_id = r.regime_id
    WHERE m.actif = 1";

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
