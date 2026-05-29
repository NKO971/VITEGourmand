<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $title ?? 'ViteGourmand'; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <?php if (isset($specificCss) && is_array($specificCss)): ?>
    <?php foreach ($specificCss as $css): ?>
        <link rel="stylesheet" href="/VITEGourmand/public/<?php echo ltrim($css, '/'); ?>">
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($specificJS) && is_array($specificJS)): ?>
    <?php foreach ($specificJS as $script): ?>
        <script src="/VITEGourmand/public/<?php echo ltrim($script, '/'); ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>
</head>
<body>
<header>
    
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/VITE&Gourmand/public/?page=home">
                <img src="" class="logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto align-items-center">
                    <a class="nav-link" href="/VITE&Gourmand/public/?page=home">Accueil</a>
                    <a class="nav-link" href="/VITEGourmand/public/?page=menus">Nos Menus</a>
                    <a class="nav-link" href="/VITE&Gourmand/public/?page=contact">Contact</a>

                    <!--  if (isset($_SESSION['user_id'])):  -->
                        
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-mon-compte" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Mon compte (
                                     <!-- echo htmlspecialchars($_SESSION['pseudo']);  -->
                                    )
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                                <li class="dropdown-item-text text-center border-bottom pb-2 mb-2">
                                    <span class="badge bg-success py-2 px-3">

                                      Crédits</span>
                                </li>
                                <li><a class="dropdown-item" href="/VITE&Gourmand/public/?page=profile">Accéder au profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="../app/controllers/logout_controller.php">Déconnexion</a></li>
                            </ul>
                        </div>

                    <!-- php else:  -->

                        <a class="nav-link" href="/EcoRide/public/?page=connexion">Connexion</a>

                    <!-- php endif; -->
                    </div>
            </div>
        </div>
    </nav>
</header> 
<body>