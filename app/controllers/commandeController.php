<?php
function commandeController($menuModel)
{
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
