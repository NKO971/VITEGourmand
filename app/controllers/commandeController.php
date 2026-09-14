<?php

function commandeController($menuModel)
{
    require_once ROOT_PATH . 'helpers/auth.php';
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

function getZoneDistance($pdo)
{
    header('Content-Type: application/json');

    $codePostal = $_GET['code_postal'] ?? null;

    if (!$codePostal) {
        echo json_encode(['error' => 'Code postal manquant']);
        return;
    }

    require_once ROOT_PATH . 'app/models/ZoneLivraison.php';
    $zoneModel = new ZoneLivraison($pdo);
    $distance = $zoneModel->getDistanceByCodePostal($codePostal);

    if ($distance !== null) {
        echo json_encode(['distance_km' => $distance]);
    } else {
        echo json_encode(['error' => 'Zone de livraison non trouvée']);
    }
}

function enregistrerCommande($pdo, $menuModel, $commandeModel, $dataPost)
{
    require_once ROOT_PATH . 'helpers/auth.php';
    requireLogin();

    $menu = $menuModel->getMenuById($dataPost['menu_id']);

    if (!$menu) {
        throw new Exception("Menu non trouvé ");
    }

    require_once ROOT_PATH . 'app/models/ZoneLivraison.php';
    $zoneModel = new ZoneLivraison($pdo);
    $distance = $zoneModel->getDistanceByCodePostal($dataPost['code_postal']);

    $resultat = $commandeModel->calculerTotalCommande(
        $menu['prix_par_personne'],
        $dataPost['nb_personnes'],
        $menu['nombre_personne_minimum'],
        $distance
    );

    if (!$resultat['zone_desservie']) {
        throw new Exception("Votre zone n'est pas desservie pour la livraison. Merci de vérifier votre code postal.");
    }

    $numeroCommande = $commandeModel->generateNumeroCommande();

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
        require_once ROOT_PATH . 'app/models/StatsCommandeModel.php';
        $statsModel = new StatsCommandeModel();
        $statsModel->upsertStats(
            (int)$pdo->lastInsertId(),
            (int)$dataPost['menu_id'],
            $resultat['total_general'],
            'En attente',
            date('Y-m-d')
        );

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
    require_once ROOT_PATH . 'helpers/auth.php';
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
    require_once ROOT_PATH . 'helpers/auth.php';
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

    require_once ROOT_PATH . 'app/models/ZoneLivraison.php';
    $zoneModel = new ZoneLivraison($pdo);
    $distance = $zoneModel->getDistanceByCodePostal($dataPost['code_postal']);

    $resultat = $commandeModel->calculerTotalCommande(
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
    require_once ROOT_PATH . 'helpers/auth.php';
    
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
