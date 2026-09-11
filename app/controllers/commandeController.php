<?php

// Vérification centralisée de connexion — évite la duplication du même bloc dans chaque fonction
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?page=connexion");
        exit();
    }
}

function commandeController($menuModel)
{
    requireLogin();

    $menuId = $_GET['menu_id'] ?? null;
    $menu = $menuModel->getMenuById($menuId);

    if (!$menu) {
        header("Location: ?page=menus");
        exit();
    }

    $user_data = [
        'nom' => $_SESSION['nom'],
        'prenom' => $_SESSION['prenom'],
        'email' => $_SESSION['email'],
        'gsm' => $_SESSION['gsm'],
        'code_postal' => $_SESSION['code_postal'] ?? '',
        'adresse' => $_SESSION['adresse'] ?? '',
    ];
    $specificCss = [];
    $specificJS = ['js/commande.js'];

    BaseController::render(
        "Finaliser ma commande - VITEGourmand",
        "commande.view.php",
        $specificCss,
        $specificJS,
        [
            'menu'      => $menu,
            'user_data' => $user_data
        ]
    );
}

// Fonction de calcul du prix total du menu
function calculerTotalCommande($prixMenu, $nbPersonnes, $minPersonnes, $distanceKM)
{
    if ($distanceKM === null) {
        return [
            'zone_desservie' => false,
            'total_menu' => 0,
            'frais_livraison' => 0,
            'total_general' => 0
        ];
    }

    $totalMenu = $prixMenu * $nbPersonnes;

    if ($nbPersonnes >= ($minPersonnes + 5)) {
        $totalMenu = $totalMenu * 0.9;
    }

    $fraisLivraison = ($distanceKM > 0) ? (5 + (0.59 * $distanceKM)) : 0;

    return [
        'zone_desservie' => true,
        'total_menu' => $totalMenu,
        'frais_livraison' => $fraisLivraison,
        'total_general' => $totalMenu + $fraisLivraison
    ];
}

function getZoneDistance($pdo)
{
    header('Content-Type: application/json');

    $codePostal = $_GET['code_postal'] ?? null;

    if (!$codePostal) {
        echo json_encode(['error' => 'Code postal manquant']);
        return;
    }

    $stmt = $pdo->prepare("SELECT distance_km FROM zone_livraison WHERE code_postal = :code_postal");
    $stmt->execute(['code_postal' => $codePostal]);
    $zone = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($zone) {
        echo json_encode(['distance_km' => $zone['distance_km']]);
    } else {
        echo json_encode(['error' => 'Zone de livraison non trouvée']);
    }
}

function getDistanceByCodePostal($pdo, $codePostal)
{
    $stmt = $pdo->prepare("SELECT distance_km FROM zone_livraison WHERE code_postal = :cp");
    $stmt->execute(['cp' => $codePostal]);
    $zone = $stmt->fetch(PDO::FETCH_ASSOC);

    // null = zone introuvable != 0 km, car certaines zones peuvent être à 0 km (ex: centre-ville)
    return $zone ? (float)$zone['distance_km'] : null;
}

function enregistrerCommande($pdo, $menuModel, $commandeModel, $dataPost)
{
    requireLogin();

    $menu = $menuModel->getMenuById($dataPost['menu_id']);

    if (!$menu) {
        throw new Exception("Menu non trouvé ");
    }

    $distance = getDistanceByCodePostal($pdo, $dataPost['code_postal']);

    $resultat = calculerTotalCommande(
        $menu['prix_par_personne'],
        $dataPost['nb_personnes'],
        $menu['nombre_personne_minimum'],
        $distance
    );

    if (!$resultat['zone_desservie']) {
        throw new Exception("Votre zone n'est pas desservie pour la livraison. Merci de vérifier votre code postal.");
    }

    $numeroCommande = 'CMD-' . uniqid();

    $success = $commandeModel->enregistrerCommande([
        'numero_commande'       => $numeroCommande,
        'date_commande'         => date('Y-m-d'),
        'date_prestation'       => $dataPost['date_prestation'],
        'heure_livraison'       => $dataPost['heure_livraison'],
        'adresse_livraison'     => $dataPost['lieu_livraison'],
        'code_postal_livraison' => $dataPost['code_postal'],
        'prix_menu'             => $resultat['total_menu'],
        'nombre_personne'       => $dataPost['nb_personnes'],
        'prix_livraison'        => $resultat['frais_livraison'],
        'utilisateur_id'        => $_SESSION['user_id'],
        'menu_id'               => $dataPost['menu_id'],
        'statut'                => 'En attente'
    ]);

    if ($success) {
        require_once ROOT_PATH . 'helpers/mailer.php';

        $mailSent = sendOrderConfirmationNotification(
            $_SESSION['email'],
            $_SESSION['nom'],
            $numeroCommande,
            $dataPost['date_prestation'],
            number_format($resultat['total_general'], 2, ',', ' ')
        );

        error_log("Tentative d'envoi de mail à : " . $_SESSION['email'] . " - Résultat : " . ($mailSent ? "Succès" : "Échec"));

        header("Location: ?page=confirmation");
        exit();
    } else {
        throw new Exception("Erreur lors de l'enregistrement de la commande.");
    }
}

function modifierCommandeController($pdo, $menuModel, $commandeModel)
{
    requireLogin();

    $commandeId = $_GET['id'] ?? null;
    if (!$commandeId) {
        header("Location: ?page=profile");
        exit();
    }

    if (!$commandeModel->canModifyOrder($commandeId, $_SESSION['user_id'])) {
        $_SESSION['flash_message'] = "Cette commande ne peut plus être modifiée.";
        header("Location: ?page=profile");
        exit();
    }

    $commande = $commandeModel->getOrderById($commandeId, $_SESSION['user_id']);
    if (!$commande) {
        header("Location: ?page=profile");
        exit();
    }

    $menu = $menuModel->getMenuById($commande['menu_id']);
    if (!$menu) {
        header("Location: ?page=profile");
        exit();
    }

    BaseController::render(
        "Modifier ma commande - VITEGourmand",
        "modifier_commande.view.php",
        [],
        ['js/commande.js'],
        [
            'commande' => $commande,
            'menu'     => $menu
        ]
    );
}

function updateCommandeController($pdo, $menuModel, $commandeModel, $dataPost)
{
    requireLogin();

    $commandeId = $dataPost['commande_id'] ?? null;

    if (!$commandeId || !$commandeModel->canModifyOrder($commandeId, $_SESSION['user_id'])) {
        $_SESSION['flash_message'] = "Cette commande ne peut plus être modifiée.";
        header("Location: ?page=profile");
        exit();
    }

    $commande = $commandeModel->getOrderById($commandeId, $_SESSION['user_id']);
    if (!$commande) {
        header("Location: ?page=profile");
        exit();
    }

    $menu = $menuModel->getMenuById($commande['menu_id']);
    if (!$menu) {
        throw new Exception("Menu introuvable pour cette commande.");
    }

    $distance = getDistanceByCodePostal($pdo, $dataPost['code_postal']);

    $resultat = calculerTotalCommande(
        $menu['prix_par_personne'],
        $dataPost['nb_personnes'],
        $menu['nombre_personne_minimum'],
        $distance
    );

    if (!$resultat['zone_desservie']) {
        $_SESSION['flash_message'] = "Votre zone n'est pas desservie pour la livraison. Merci de vérifier votre code postal.";
        header("Location: ?page=modifier_commande&id={$commandeId}");
        exit();
    }

    $success = $commandeModel->updateOrder($commandeId, $_SESSION['user_id'], [
        'date_prestation'       => $dataPost['date_prestation'],
        'heure_livraison'       => $dataPost['heure_livraison'],
        'adresse_livraison'     => $dataPost['lieu_livraison'],
        'code_postal_livraison' => $dataPost['code_postal'],
        'nombre_personne'       => $dataPost['nb_personnes'],
        'prix_menu'             => $resultat['total_menu'],
        'prix_livraison'        => $resultat['frais_livraison']
    ]);

    if ($success) {
        $_SESSION['flash_message'] = "Commande modifiée avec succès.";
    } else {
        $_SESSION['flash_message'] = "Impossible de modifier cette commande.";
    }

    header("Location: ?page=profile");
    exit();
}

function annulerCommandeController($pdo)
{
    require_once __DIR__ . '/../models/Commande.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    requireLogin();

    $commandeId = $_GET['id'] ?? null;

    if (!$commandeId) {
        header("Location: ?page=profile");
        exit();
    }

    $commandeModel = new Commande($pdo);

    $succes = $commandeModel->cancelOrder($commandeId, $_SESSION['user_id'], 'Annulée');

    if ($succes) {
        $_SESSION['flash_message'] = "Commande annulée avec succès.";
    } else {
        $_SESSION['flash_message'] = "Impossible d'annuler cette commande (déjà acceptée ou inexistante).";
    }

    header("Location: ?page=profile");
    exit();
}
