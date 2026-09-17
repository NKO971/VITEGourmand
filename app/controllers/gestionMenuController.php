<?php

function renderGestionMenuController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2]);

    require_once ROOT_PATH . 'app/models/Menu.php';
    require_once ROOT_PATH . 'app/models/Plat.php';
    require_once ROOT_PATH . 'app/models/Theme.php';
    require_once ROOT_PATH . 'app/models/Regime.php';
    require_once ROOT_PATH . 'app/models/Allergene.php';

    try {
        $menuModel = new Menu($pdo);
        $platModel = new Plat($pdo);
        $themeModel = new Theme($pdo);
        $regimeModel = new Regime($pdo);
        $allergeneModel = new Allergene($pdo);

        $menus = $menuModel->getAllWithLabels();
        $plats = $platModel->getAll();
        $themes = $themeModel->getAll();
        $regimes = $regimeModel->getAll();
        $allergenes = $allergeneModel->getAll();

        // Allergenes deja associes a chaque plat, pour pre-remplir le
        // select multiple en edition (data-allergenes sur le bouton Modifier).
        $allergenesParPlat = $platModel->getAllergenesForPlats(array_column($plats, 'plat_id'));
    } catch (PDOException $e) {
        error_log("Erreur chargement carte back-office : " . $e->getMessage());
        $menus = [];
        $plats = [];
        $themes = [];
        $regimes = [];
        $allergenes = [];
        $allergenesParPlat = [];
    }

    $currentPage = 'employee_menus';

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
        "Gestion de la Carte",
        "gestion_carte_plats.view.php",
        [],
        ['js/dashboard_menus_plats.js'],
        [
            'menus'              => $menus,
            'plats'              => $plats,
            'themes'             => $themes,
            'regimes'            => $regimes,
            'allergenes'         => $allergenes,
            'allergenesParPlat'  => $allergenesParPlat,
            'currentPage'        => 'employee_menus'
        ],
        'back'
    );
}

function createMenuController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);

    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            $data = $_POST;
        }

        $titre        = trim(strip_tags($data['titre'] ?? ''));
        $prix         = filter_var($data['prix'] ?? null, FILTER_VALIDATE_FLOAT);
        $stock        = filter_var($data['stock'] ?? null, FILTER_VALIDATE_INT);
        $minPersonnes = filter_var($data['min_personnes'] ?? null, FILTER_VALIDATE_INT);
        $themeId      = filter_var($data['theme_id'] ?? null, FILTER_VALIDATE_INT);
        $regimeId     = filter_var($data['regime_id'] ?? null, FILTER_VALIDATE_INT);
        $description  = trim(strip_tags($data['description'] ?? ''));

        $compositions       = $data['composition'] ?? null;
        $conditionsStockage = $data['conditions_stockage'] ?? null;

        // Délai de commande structuré (valeur + unité heures/jours). Optionnel :
        // absent ou vide = pas de délai minimum pour ce menu.
        $delaiValeurRaw = $data['delai_valeur'] ?? null;
        $delaiUniteRaw  = trim((string)($data['delai_unite'] ?? ''));
        $delaiValeur = ($delaiValeurRaw === null || $delaiValeurRaw === '') ? null : filter_var($delaiValeurRaw, FILTER_VALIDATE_INT);
        $delaiUnite  = $delaiUniteRaw !== '' ? $delaiUniteRaw : null;

        if (empty($titre) || $prix === false || $prix <= 0 || $stock === false || $stock < 0 || !$themeId || !$regimeId || !$minPersonnes || $minPersonnes < 1) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Champs invalides ou incomplets (vérifiez titre, prix, stock, thème, régime et nombre de personnes minimum).'
            ]);
            exit();
        }

        if ($delaiValeur !== null && ($delaiValeur === false || $delaiValeur <= 0 || !in_array($delaiUnite, ['heures', 'jours'], true))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Délai de commande invalide (valeur positive et unité heures/jours requises).'
            ]);
            exit();
        }
        if ($delaiValeur === null) {
            $delaiUnite = null;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            require_once ROOT_PATH . 'helpers/uploadImage.php'; // helper réel : uploadImage.php
            $uploadResult = moveUploadedImage($_FILES['image'], 'menu');

            if (!$uploadResult['success']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $uploadResult['error']]);
                exit();
            }

            $imagePath = $uploadResult['path'];
        }

        require_once ROOT_PATH . 'app/models/Menu.php';
        $menuModel = new Menu($pdo);

        $menuId = $menuModel->createMenu([
            'titre'               => $titre,
            'description'         => $description,
            'prix'                => $prix,
            'stock'               => $stock,
            'min_personnes'       => $minPersonnes,
            'theme_id'            => $themeId,
            'regime_id'           => $regimeId,
            'composition'         => $compositions,
            'conditions_stockage' => $conditionsStockage,
            'delai_commande_valeur' => $delaiValeur,
            'delai_commande_unite'  => $delaiUnite,
            'image'               => $imagePath
        ]);

        // Galerie : textarea "une URL par ligne" (peut arriver en string ou en tableau)
        $rawGalerie = $data['galerie'] ?? $data['images'] ?? $data['galerie_urls'] ?? '';
        $urlsGalerie = is_array($rawGalerie) ? $rawGalerie : Menu::parseGalleryTextarea((string)$rawGalerie);
        if (!empty($urlsGalerie)) {
            $menuModel->saveGallery((int)$menuId, $urlsGalerie);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Menu créé avec succès.',
            'menu_id' => $menuId
        ]);
        exit();
    } catch (PDOException $e) {
        error_log("Erreur PDO createMenu : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    } catch (Throwable $e) {
        error_log("Erreur Serveur createMenu : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur Serveur PHP : ' . $e->getMessage()]);
        exit();
    }
}

function updateMenuController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);


    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            $data = $_POST;
        }

        $menuId       = filter_var($data['menu_id'] ?? null, FILTER_VALIDATE_INT);
        $titre        = trim(strip_tags($data['titre'] ?? ''));
        $prix         = filter_var($data['prix'] ?? null, FILTER_VALIDATE_FLOAT);
        $stock        = filter_var($data['stock'] ?? null, FILTER_VALIDATE_INT);
        $minPersonnes = filter_var($data['min_personnes'] ?? null, FILTER_VALIDATE_INT);
        $themeId      = filter_var($data['theme_id'] ?? null, FILTER_VALIDATE_INT);
        $regimeId     = filter_var($data['regime_id'] ?? null, FILTER_VALIDATE_INT);
        $description  = trim(strip_tags($data['description'] ?? ''));

        $compositions       = $data['composition'] ?? null;
        $conditionsStockage = $data['conditions_stockage'] ?? null;

        // Délai de commande structuré (valeur + unité heures/jours). Optionnel :
        // absent ou vide = pas de délai minimum pour ce menu.
        $delaiValeurRaw = $data['delai_valeur'] ?? null;
        $delaiUniteRaw  = trim((string)($data['delai_unite'] ?? ''));
        $delaiValeur = ($delaiValeurRaw === null || $delaiValeurRaw === '') ? null : filter_var($delaiValeurRaw, FILTER_VALIDATE_INT);
        $delaiUnite  = $delaiUniteRaw !== '' ? $delaiUniteRaw : null;

        if (!$menuId || empty($titre) || $prix === false || $prix <= 0 || $stock === false || $stock < 0 || !$themeId || !$regimeId || !$minPersonnes || $minPersonnes < 1) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Champs invalides ou incomplets (Vérifiez le titre, le prix, le stock, le thème et le régime).'
            ]);
            exit();
        }

        if ($delaiValeur !== null && ($delaiValeur === false || $delaiValeur <= 0 || !in_array($delaiUnite, ['heures', 'jours'], true))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Délai de commande invalide (valeur positive et unité heures/jours requises).'
            ]);
            exit();
        }
        if ($delaiValeur === null) {
            $delaiUnite = null;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            require_once ROOT_PATH . 'helpers/uploadImage.php'; // helper réel : uploadImage.php
            $uploadResult = moveUploadedImage($_FILES['image'], 'menu');

            if (!$uploadResult['success']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $uploadResult['error']]);
                exit();
            }

            $imagePath = $uploadResult['path'];
        }

        require_once ROOT_PATH . 'app/models/Menu.php';
        $menuModel = new Menu($pdo);

        $menuModel->updateMenu([
            'menu_id'             => $menuId,
            'titre'               => $titre,
            'description'         => $description,
            'prix'                => $prix,
            'stock'               => $stock,
            'min_personnes'       => $minPersonnes,
            'theme_id'            => $themeId,
            'regime_id'           => $regimeId,
            'composition'         => $compositions,
            'conditions_stockage' => $conditionsStockage,
            'delai_commande_valeur' => $delaiValeur,
            'delai_commande_unite'  => $delaiUnite,
            'image'               => $imagePath
        ]);

        // Galerie : si le champ est présent dans la requête, on remplace
        // toute la galerie (champ vide = suppression de la galerie).
        if (array_key_exists('galerie', $data) || array_key_exists('images', $data) || array_key_exists('galerie_urls', $data)) {
            $rawGalerie = $data['galerie'] ?? $data['images'] ?? $data['galerie_urls'] ?? '';
            $urlsGalerie = is_array($rawGalerie) ? $rawGalerie : Menu::parseGalleryTextarea((string)$rawGalerie);
            $menuModel->saveGallery((int)$menuId, $urlsGalerie);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Menu mis à jour avec succès.',
            'image'   => $imagePath
        ]);
        exit();
    } catch (PDOException $e) {
        error_log("Erreur PDO updateMenu : " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'Erreur BDD SQL : ' . $e->getMessage()
        ]);
        exit();
    } catch (Throwable $e) {
        error_log("Erreur Serveur updateMenu : " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'Erreur Serveur PHP : ' . $e->getMessage()
        ]);
        exit();
    }
}

function toggleMenuStatusController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?? [];

    $menuId = $data['menu_id'] ?? null;
    $actif  = isset($data['actif']) ? (int)$data['actif'] : null;

    if (!$menuId || !in_array($actif, [0, 1], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifiant du menu ou statut invalide.']);
        exit();
    }

    require_once ROOT_PATH . 'app/models/Menu.php';
    $menuModel = new Menu($pdo);

    try {
        $rowsAffected = $menuModel->toggleStatus($menuId, $actif);

        if ($rowsAffected === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Menu introuvable ou aucun changement effectué.']);
            exit();
        }

        echo json_encode([
            'success' => true,
            'message' => $actif === 1 ? 'Menu réactivé avec succès.' : 'Menu masqué avec succès.'
        ]);
        exit();
    } catch (PDOException $e) {
        error_log("Erreur PDO toggleMenuStatus : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}

function getMenuGalleryController($pdo)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireRole([1, 2], true);

    $menuId = filter_var($_GET['menu_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$menuId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'menu_id invalide.']);
        exit();
    }

    try {
        require_once ROOT_PATH . 'app/models/Menu.php';
        $menuModel = new Menu($pdo);
        echo json_encode(['success' => true, 'images' => $menuModel->getImagesByMenuId($menuId)]);
        exit();
    } catch (PDOException $e) {
        error_log("Erreur PDO getMenuGallery : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}
