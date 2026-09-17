<?php
function menusController($pdo)
{
    require_once ROOT_PATH . 'app/models/Menu.php';
    require_once ROOT_PATH . 'app/models/Theme.php';
    require_once ROOT_PATH . 'app/models/Regime.php';
    require_once ROOT_PATH . 'app/models/Plat.php';

    try {
        $themeModel = new Theme($pdo);
        $regimeModel = new Regime($pdo);
        $menuModel = new Menu($pdo);
        $platModel = new Plat($pdo);

        $themes = $themeModel->getAll();
        $regimes = $regimeModel->getAll();
        $menus = $menuModel->getAllActiveWithLabels();

        // Galeries photos (table menu_images) : 1 seule requête pour tous les menus
        $galeries = [];
        try {
            $ids = array_column($menus, 'menu_id');
            $galeries = $menuModel->getAllImagesByMenuIds($ids);
        } catch (PDOException $e) {
            error_log("Erreur chargement galeries menus : " . $e->getMessage());
            $galeries = [];
        }

        // État actuel des plats, indexé à la fois par ID (fiable) et par nom normalisé (repli pour anciennes données)
        $platsParId = [];
        $platsParNom = [];
        $tousLesPlats = $platModel->getAll();
        foreach ($tousLesPlats as $p) {
            $platsParId[$p['plat_id']] = $p;
            $platsParNom[mb_strtolower(trim($p['titre_plat']))] = $p;
        }

        // Allergenes de chaque plat (table plat_allergene), source de verite
        // affichee au visiteur - remplace l'ancien JSON libre composition.allergenes.
        $allergenesParPlat = $platModel->getAllergenesForPlats(array_column($tousLesPlats, 'plat_id'));

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
                        $composition[$role]['plat_id'] = $platTrouve['plat_id'];
                        $composition[$role]['allergenes'] = array_column(
                            $allergenesParPlat[$platTrouve['plat_id']] ?? [],
                            'libelle'
                        );
                    } else {
                        $composition[$role]['nom'] = 'Actuellement indisponible';
                        $composition[$role]['allergenes'] = [];
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

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    BaseController::render(
        "Nos Menus",
        "menus.view.php",
        ["css/page-menu.css"],
        ["js/menus-filter.js"],
        [
            'themes'  => $themes,
            'regimes' => $regimes,
            'menus'   => $menus,
            'galeries' => $galeries ?? [],
            'horairesFooter' => $horairesFooter
        ]
    );
}
