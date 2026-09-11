<?php
function menusController()
{
    global $pdo;

    try {
        $themes = $pdo->query("SELECT * FROM " . TABLE_THEME)->fetchAll();
        $regimes = $pdo->query("SELECT * FROM " . TABLE_REGIME)->fetchAll();

        $menusQuery = "SELECT
            m.*,
            t.libelle AS theme_libelle,
            r.libelle AS regime_libelle
        FROM " . TABLE_MENU . " m
        LEFT JOIN " . TABLE_THEME . " t ON m.theme_id = t.theme_id
        LEFT JOIN " . TABLE_REGIME . " r ON m.regime_id = r.regime_id
        WHERE m.actif = 1";

        $menus = $pdo->query($menusQuery)->fetchAll();

        // État actuel des plats, indexé à la fois par ID (fiable) et par nom normalisé (repli pour anciennes données)
        $platsParId = [];
        $platsParNom = [];
        $stmtPlats = $pdo->query("SELECT plat_id, titre_plat, actif FROM plat");
        foreach ($stmtPlats->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $platsParId[$p['plat_id']] = $p;
            $platsParNom[mb_strtolower(trim($p['titre_plat']))] = $p;
        }

        foreach ($menus as &$menu) {
            $composition = json_decode($menu['composition'] ?? '[]', true);
            if (is_array($composition)) {
                foreach (['entree', 'plat', 'dessert'] as $role) {
                    if (empty($composition[$role])) {
                        continue;
                    }

                    $platTrouve = null;

                    // Priorité 1 : matching fiable par plat_id (cas des menus sauvegardés récemment)
                    if (!empty($composition[$role]['plat_id']) && isset($platsParId[$composition[$role]['plat_id']])) {
                        $platTrouve = $platsParId[$composition[$role]['plat_id']];
                    }
                    // Priorité 2 (repli) : matching approximatif par nom (anciennes données sans plat_id)
                    elseif (!empty($composition[$role]['nom'])) {
                        $cle = mb_strtolower(trim($composition[$role]['nom']));
                        if (isset($platsParNom[$cle])) {
                            $platTrouve = $platsParNom[$cle];
                        }
                    }

                    if ($platTrouve && $platTrouve['actif'] == 1) {
                        $composition[$role]['nom'] = $platTrouve['titre_plat'];
                        $composition[$role]['plat_id'] = $platTrouve['plat_id']; // On en profite pour "réparer" les anciennes données sans ID
                    } else {
                        $composition[$role]['nom'] = 'Actuellement indisponible';
                    }
                }
            }
            $menu['composition'] = json_encode($composition, JSON_UNESCAPED_UNICODE);
        }
        unset($menu);

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des menus : " . $e->getMessage());
        $themes = [];
        $regimes = [];
        $menus = [];
    }

    require_once(__DIR__ . '/baseController.php');

    BaseController::render(
        "Nos Menus",
        "menus.view.php",
        ["css/page-menu.css"],
        ["js/menus-filter.js"],
        [
            'themes'  => $themes,
            'regimes' => $regimes,
            'menus'   => $menus
        ]
    );
}