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
        echo json_encode(['error' => 'Erreur lors de la mise à jour en base de données.']);
        exit();
    }
}

function updateMenuController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé.']);
        exit();
    }

    // Récupération et extraction des données POST
    $menuId      = $_POST['menu_id'] ?? null;
    $titre       = trim(strip_tags($_POST['titre'] ?? '')); // Nettoyage des balises HTML
    $description = trim(strip_tags($_POST['description'] ?? ''));
    $prix        = filter_var($_POST['prix'] ?? null, FILTER_VALIDATE_FLOAT);

    // Validation des champs obligatoires
    if (!$menuId || empty($titre) || $prix === false || $prix <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs invalides ou incomplets (le prix doit être un nombre positif).']);
        exit();
    }

    // Gestion de l'image uploadée
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        
        // Sécurité Fichier 
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileMimeType     = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format d\'image non autorisé (Seuls JPG, PNG et WEBP sont acceptés).']);
            exit();
        }

        // Génération d'un nom de fichier unique
        $extension  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName   = 'menu_' . uniqid() . '.' . $extension;
        $uploadDir  = ROOT_PATH . 'public/assets/images/';
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Échec du transfert de l\'image sur le serveur.']);
            exit();
        }

        $imagePath = 'assets/images/' . $fileName;
    }

    // Préparation et exécution de la requête SQL pour la mise à jour
    try {
        if ($imagePath) {
            // Si une nouvelle image est uploadée mise a jour du chemin de l'image dans la base de données
            $sql = "UPDATE menu 
                    SET titre = :titre, description = :description, prix = :prix, image = :image 
                    WHERE menu_id = :id";
            $params = [
                ':titre'       => $titre,
                ':description' => $description,
                ':prix'        => $prix,
                ':image'       => $imagePath,
                ':id'          => $menuId
            ];
        } else {
            // Sinon on conserve l'ancienne image
            $sql = "UPDATE menu 
                    SET titre = :titre, description = :description, prix = :prix 
                    WHERE menu_id = :id";
            $params = [
                ':titre'       => $titre,
                ':description' => $description,
                ':prix'        => $prix,
                ':id'          => $menuId
            ];
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
        echo json_encode(['error' => 'Erreur lors de la mise à jour du menu.']);
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

    // 3. Validation
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
        echo json_encode(['error' => 'Erreur lors de la mise à jour en BDD.']);
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
        // Validation du type MIME réel
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileMime     = mime_content_type($_FILES['photo']['tmp_name']);

        if (!in_array($fileMime, $allowedMimes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format d\'image invalide (JPG, PNG, WEBP uniquement).']);
            exit();
        }

        // Lecture du flux binaire pour insertion BDD
        $photoData = file_get_contents($_FILES['photo']['tmp_name']);
    }

    try {
        if ($photoData !== null) {
            $sql = "UPDATE plat SET titre_plat = :titre, photo = :photo WHERE plat_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':titre', $titrePlat);
            $stmt->bindValue(':photo', $photoData, PDO::PARAM_LOB); // Spécifique au stockage BLOB
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
        echo json_encode(['error' => 'Erreur lors de la modification du plat.']);
        exit();
    }
}

function renderGestionCarteController($pdo) {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
        header('Location: ?page=login');
        exit();
    }

    // Récupération de TOUS les menus et plats (y compris ceux avec actif = 0)
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

    } catch (PDOException $e) {
        error_log("Erreur chargement carte back-office : " . $e->getMessage());
        $menus = [];
        $plats = [];
    }

    $currentPage = 'employee_menus';

    require_once ROOT_PATH . 'app/controllers/baseController.php';
    BaseController::render(
    "Gestion de la Carte",
    "gestion_carte_plats.view.php",
    [],
    ['js/gestion-carte.js'],
    [
        'menus'       => $menus,
        'plats'       => $plats,
        'currentPage' => 'employee_menus'
    ],
    'back'
);
}
