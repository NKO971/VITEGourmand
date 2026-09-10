<?php

function toggleMenuStatusController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403); // 403 = Interdit / Accès refusé
        echo json_encode(['error' => 'Accès refusé. Droits insuffisants.']);
        exit();
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?? [];

    $menuId = $data['menu_id'] ?? null;
    $actif  = isset($data['actif']) ? (int)$data['actif'] : null;

    if (!$menuId || !in_array($actif, [0, 1], true)) {
        http_response_code(400); // 400 = Requête incorrecte
        echo json_encode(['error' => 'Identifiant du menu ou statut invalide.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE menu SET actif = :actif WHERE menu_id = :id");
        $stmt->execute([
            ':actif' => $actif,
            ':id'    => $menuId
        ]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404); // 404 = Menu introuvable
            echo json_encode(['error' => 'Menu introuvable ou aucun changement effectué.']);
            exit();
        }

        // Succès
        echo json_encode([
            'success' => true,
            'message' => $actif === 1 ? 'Menu réactivé avec succès.' : 'Menu masqué avec succès.'
        ]);
        exit();

    } catch (PDOException $e) {
        error_log("Erreur PDO toggleMenuStatus : " . $e->getMessage());
        http_response_code(500); // 500 = Erreur interne du serveur
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}

// Crétation d'un nouveau plat
function createPlatController(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée.']);
        return;
    }

    $titrePlat = isset($_POST['titre_plat']) ? trim(strip_tags($_POST['titre_plat'])) : '';
    $actif = isset($_POST['actif']) ? (int)$_POST['actif'] : 1;

    if (empty($titrePlat)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Le titre du plat est obligatoire.']);
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO plat (titre_plat, actif) VALUES (:titre, :actif)');
        $stmt->execute([
            ':titre' => $titrePlat,
            ':actif' => $actif
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Plat créé avec succès.',
            'plat_id' => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du plat en base de données.']);
    }
}

function updateMenuController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Accès refusé.']);
        exit();
    }

    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            $data = $_POST;
        }

        $menuId      = filter_var($data['menu_id'] ?? null, FILTER_VALIDATE_INT);
        $titre       = trim(strip_tags($data['titre'] ?? ''));
        $prix        = filter_var($data['prix'] ?? null, FILTER_VALIDATE_FLOAT);
        $stock       = filter_var($data['stock'] ?? null, FILTER_VALIDATE_INT);
        $themeId     = filter_var($data['theme_id'] ?? null, FILTER_VALIDATE_INT);
        $regimeId    = filter_var($data['regime_id'] ?? null, FILTER_VALIDATE_INT);
        $description = trim(strip_tags($data['description'] ?? ''));

        $compositions       = $data['composition'] ?? null;
        $conditionsStockage = $data['conditions_stockage'] ?? null;

        if (!$menuId || empty($titre) || $prix === false || $prix <= 0 || $stock === false || $stock < 0 || !$themeId || !$regimeId) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Champs invalides ou incomplets (Vérifiez le titre, le prix, le stock, le thème et le régime).'
            ]);
            exit();
        }

        // Gestion de l'image uploadée si présente
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileMimeType     = mime_content_type($_FILES['image']['tmp_name']);

            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Format d\'image non autorisé (Seuls JPG, PNG et WEBP sont acceptés).']);
                exit();
            }

            $extension  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName   = 'menu_' . uniqid() . '.' . $extension;
            $uploadDir  = ROOT_PATH . 'public/assets/images/';
            $targetPath = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Échec du transfert de l\'image sur le serveur.']);
                exit();
            }

            $imagePath = 'assets/images/' . $fileName;
        }

        // Préparation de la requête SQL avec gestion conditionnelle de l'image
        $sql = "UPDATE menu 
                SET titre = :titre, 
                    description = :description, 
                    prix_par_personne = :prix, 
                    quantite_restante = :stock, 
                    theme_id = :theme_id, 
                    regime_id = :regime_id, 
                    composition = :composition, 
                    conditions_stockage = :conditions_stockage"
                . ($imagePath ? ", image = :image" : "") . " 
                WHERE menu_id = :id";

        $params = [
            ':titre'               => $titre,
            ':description'         => $description,
            ':prix'                => $prix,
            ':stock'               => $stock,
            ':theme_id'            => $themeId,
            ':regime_id'           => $regimeId,
            ':composition'         => $compositions,
            ':conditions_stockage' => $conditionsStockage,
            ':id'                  => $menuId
        ];

        if ($imagePath) {
            $params[':image'] = $imagePath;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

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


function togglePlatStatusController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $platId = $data['plat_id'] ?? null;
    $actif  = isset($data['actif']) ? (int)$data['actif'] : null;

    if (!$platId || !in_array($actif, [0, 1], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE plat SET actif = :actif WHERE plat_id = :id");
        $stmt->execute([
            ':actif' => $actif,
            ':id'    => $platId
        ]);

        echo json_encode([
            'success' => true,
            'message' => $actif === 1 ? 'Plat réactivé.' : 'Plat masqué (archivé).'
        ]);
        exit();

    } catch (PDOException $e) {
        error_log("Erreur togglePlatStatus : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}

function updatePlatController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    $platId    = $_POST['plat_id'] ?? null;
    $titrePlat = trim(strip_tags($_POST['titre_plat'] ?? ''));

    if (!$platId || empty($titrePlat)) {
        http_response_code(400);
        echo json_encode(['error' => 'Le titre du plat est obligatoire.']);
        exit();
    }

    $photoData = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileMime     = mime_content_type($_FILES['photo']['tmp_name']);

        if (!in_array($fileMime, $allowedMimes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format d\'image invalide (JPG, PNG, WEBP uniquement).']);
            exit();
        }

        $photoData = file_get_contents($_FILES['photo']['tmp_name']);
    }

    try {
        if ($photoData !== null) {
            $sql = "UPDATE plat SET titre_plat = :titre, photo = :photo WHERE plat_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':titre', $titrePlat);
            $stmt->bindValue(':photo', $photoData, PDO::PARAM_LOB);
            $stmt->bindValue(':id', $platId, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = "UPDATE plat SET titre_plat = :titre WHERE plat_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titre' => $titrePlat,
                ':id'    => $platId
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Plat mis à jour avec succès.']);
        exit();

    } catch (PDOException $e) {
        error_log("Erreur updatePlat : " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erreur BDD SQL : ' . $e->getMessage()]);
        exit();
    }
}

function renderGestionCarteController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        header('Location: ?page=login');
        exit();
    }

    try {
        $stmtMenus = $pdo->query("
            SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle 
            FROM menu m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            LEFT JOIN regime r ON m.regime_id = r.regime_id
            ORDER BY m.menu_id DESC
        ");
        $menus = $stmtMenus->fetchAll(PDO::FETCH_ASSOC);

        $stmtPlats = $pdo->query("SELECT * FROM plat ORDER BY plat_id DESC");
        $plats = $stmtPlats->fetchAll(PDO::FETCH_ASSOC);

        $stmtThemes = $pdo->query("SELECT * FROM theme ORDER BY libelle ASC");
        $themes = $stmtThemes->fetchAll(PDO::FETCH_ASSOC);

        $stmtRegimes = $pdo->query("SELECT * FROM regime ORDER BY libelle ASC");
        $regimes = $stmtRegimes->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur chargement carte back-office : " . $e->getMessage());
        $menus = [];
        $plats = [];
        $themes = [];
        $regimes = [];
    }

    $currentPage = 'employee_menus';

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
        "Gestion de la Carte",
        "gestion_carte_plats.view.php",
        [],
        ['js/dashboard_menus_plats.js'],
        [
            'menus'       => $menus,
            'plats'       => $plats,
            'themes'      => $themes,
            'regimes'     => $regimes,
            'currentPage' => 'employee_menus'
        ],
        'back'
    );
}
