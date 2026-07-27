<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $title ?? 'ViteGourmand'; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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
<header class="vg-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/VITEGourmand/public/?page=home">
                <span class="fw-black fs-3 text-dark font-poppins">VITE<span class="text-orange">Gourmand</span></span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto align-items-center gap-2">
                    <a class="nav-link vg-nav-link" href="/VITEGourmand/public/?page=home">Accueil</a>
                    <a class="nav-link vg-nav-link" href="/VITEGourmand/public/?page=menus">Nos Menus</a>
                    <a class="nav-link vg-nav-link" href="/VITEGourmand/public/?page=contact">Contact</a>

                    <div class="nav-vr d-none d-lg-block mx-2"></div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-vg-user shadow-sm" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span><?= htmlspecialchars($_SESSION['prenom'] ?? $_SESSION['email'] ?? 'Mon Compte'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2" aria-labelledby="navbarDropdown">
                                
                                <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 2])): ?>
                                    <li>
                                        <a class="dropdown-item py-2 fw-bold text-primary" href="/VITEGourmand/public/?page=employee">
                                            <i class="bi bi-speedometer2 me-2"></i> Espace Employé
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                <?php endif; ?>

                                <li>
                                    <a class="dropdown-item py-2" href="/VITEGourmand/public/?page=profile">
                                        <i class="bi bi-gear me-2"></i> Mon Profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="index.php?page=deconnexion">
                                        <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </div>

                    <?php else: ?>
                        <a class="nav-link vg-nav-link px-3" href="/VITEGourmand/public/?page=connexion">Connexion</a>
                        <a class="btn btn-vg-primary btn-sm px-4 py-2 fw-bold" href="/VITEGourmand/public/?page=inscription">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
