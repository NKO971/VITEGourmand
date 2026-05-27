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
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="/EcoRide/public/?page=employee">
                    <img src="/EcoRide/Image/EcoRide.svg" alt="logo EcoRide" class="logo me-2">
                    <span class="ms-2 border-start ps-2">Espace Back Office</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav ms-auto align-items-center">
                        <a class="nav-link" href="#section-avis">Modération Avis</a>
                        <a class="nav-link" href="#section-litiges">Gestion Litiges</a>

                        <div class="ms-lg-4 d-flex align-items-center">
                            <span class="navbar-text me-3 text-white d-none d-lg-inline">
                                <span class="material-symbols-outlined align-middle">account_circle</span>
                                Session : Employé
                            </span>
                            <a href="../app/controllers/logout_controller.php" type="button" id="btnLogout" class="btn btn-outline-danger btn-sm">
                                Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>