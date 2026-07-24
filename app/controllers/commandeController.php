<?php
function commandeController($menuModel)
{
    // Sécurité : Vérification connexion utilisateur
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?page=connexion");
        exit();
    }

    $menuId = $_GET['menu_id'] ?? null;
    $menu = $menuModel->getMenuById($menuId);

    // Redirection si le menu n'existe pas
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
    $totalMenu = $prixMenu * $nbPersonnes;

    // Réduction de 10% pour les commandes de 5 personnes ou plus
    if ($nbPersonnes >= ($minPersonnes + 5)) {
        $totalMenu = $totalMenu * 0.9;
    }

    $fraisLivraison = ($distanceKM > 0) ? (5 + (0.59 * $distanceKM)) : 0;

    return [
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

    return $zone ? (float)$zone['distance_km'] : 0.0;
}

function enregistrerCommande($pdo, $menuModel, $commandeModel, $dataPost)
{
    $menu = $menuModel->getMenuById($dataPost['menu_id']);

    if (!$menu) {
        throw new Exception("Menu non trouvé ");
    }

    $distance = getDistanceByCodePostal($pdo, $dataPost['code_postal']);

    $resultat = calculerTotalCommande(
        $menu['prix_par_personne'],
        $dataPost['nb_personnes'],
        $menu['min_personnes'],
        $distance
    );

    $success = $commandeModel->enregistrerCommande([
        'numero_commande' => 'CMD-' . uniqid(), // NOT NULL
        'date_commande'   => date('Y-m-d'), // NOT NULL
        'date_prestation' => $dataPost['date_prestation'],
        'heure_livraison' => $dataPost['heure_livraison'],
        'prix_menu'       => $resultat['total_menu'],
        'nombre_personne' => $dataPost['nb_personnes'],
        'prix_livraison'  => $resultat['frais_livraison'],
        'utilisateur_id'  => $_SESSION['user_id'],
        'menu_id'         => $dataPost['menu_id'],
        'statut'          => 'En attente'
    ]);

    if ($success) {
        $to = $_SESSION['email'];
        $subject = "Confirmation de votre commande - VITEGourmand";
        $message = "Bonjour " . $_SESSION['nom'] . ", votre commande a été enregistrée avec succès !";
        $headers = "From: no-reply@vitegourmand.fr\r\nReply-To: no-reply@vitegourmand.fr";

        $mailSent = mail($to, $subject, $message, $headers);

        error_log("Tentative d'envoi de mail à : " . $to . " - Résultat : " . ($mailSent ? "Succès" : "Échec"));

        header("Location: ?page=confirmation");
        exit();
    } else {
        throw new Exception("Erreur lors de l'enregistrement de la commande.");
    }

}

function annulerCommandeController($pdo)
    {
        require_once __DIR__ . '/../models/Commande.php';
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: ?page=connexion");
            exit();
        }

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
