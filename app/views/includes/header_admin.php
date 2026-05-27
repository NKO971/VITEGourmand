<?php 
// Indispensable pour que le header sache qui est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// On n'appelle la BDD que si l'utilisateur est connecté pour récupérer ses crédits
$credits_reels = 0; 
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../app/config/db.php'; // Chemin sécurisé (includes/ → remonte 1 → app/)
    
    $stmt = $pdo->prepare("SELECT solde_credits FROM utilisateur WHERE utilisateur_id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $resultat = $stmt->fetch();
    
    if ($resultat) {
        $credits_reels = $resultat['solde_credits'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
<?php 
echo $title ?? 'EcoRide'; 
?>
    </title>
         <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
         <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

<?php if (isset($specificCss)): ?>
    <?php foreach ($specificCss as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($specificJS)): ?>
    <?php foreach ($specificJS as $script): ?>
        <script src="<?= $script ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/EcoRide/public/?page=admin">
                <img src="/EcoRide/Image/EcoRide.svg" alt="Logo EcoRide" class="me-2">
                <span class="badge bg-danger ms-2" style="font-size: 0.5em;">ADMIN</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/EcoRide/public/?page=admin">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/EcoRide/public/">Voir le site</a>
                    </li>
                    <li class="nav-item">
                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item d-flex align-items-center me-lg-4">
                                <div class="text-end d-none d-lg-block me-2">
                                    <p class="mb-0 text-light-dark fw-bold" id="nom-admin">Chargement...</p>
                                </div>
                                <a class="btn btn-outline-light btn-sm ms-lg-3 mt-2 mt-lg-0" 
                                   href="../app/controllers/logout_controller.php" 
                                   id="btn-logout-admin">Déconnexion</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>