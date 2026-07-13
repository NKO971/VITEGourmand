<div class="container-fluid">
    <div class="row">
        
        <!-- SIDEBAR LATÉRALE GAUCHE -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark text-white min-vh-100 p-3 shadow">
            <div class="position-sticky pt-3">
                <div class="text-center mb-4">
                    <h5 class="text-primary fw-bold mb-0">VITEGourmand</h5>
                    <small class="text-muted">Espace Employé</small>
                </div>
                <hr class="text-muted">
                
                <!-- Liens de navigation du Dashboard -->
                <ul class="nav flex-column gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-white bg-primary rounded px-3 py-2 d-flex align-items-center gap-2" href="?page=employee">
                            <i class="bi bi-speedometer2"></i> Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3 py-2 d-flex align-items-center gap-2" href="#">
                            <i class="bi bi-egg-fried"></i> Menus & Plats
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3 py-2 d-flex align-items-center gap-2" href="#">
                            <i class="bi bi-clock"></i> Horaires
                        </a>
                    </li>
                    <li class="nav-item text-white-50 px-3 py-2 d-flex align-items-center gap-2">
                        <a class="nav-link text-white-50 p-0" href="#">
                            <i class="bi bi-chat-left-text"></i> Modération Avis
                        </a>
                    </li>
                </ul>
                
                <hr class="text-muted mt-4">
                
                <!-- Liens de sortie -->
                <ul class="nav flex-column gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-info px-3 py-2 d-flex align-items-center gap-2" href="?page=home">
                            <i class="bi bi-arrow-left-circle"></i> Retour au site public
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger px-3 py-2 d-flex align-items-center gap-2" href="?page=deconnexion">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- CONTENU PRINCIPAL (À DROITE) -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 bg-light">
            <!-- En-tête de la zone de travail -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestion des commandes</h1>
                <div class="text-muted">
                    Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></strong>
                </div>
            </div>

            <!-- Zone temporaire pour voir l'évolution -->
            <div class="card shadow-sm border-0 p-4 mb-4">
                <h5 class="card-title fw-bold">Statut du chantier</h5>
                <p class="text-muted mb-0">
                    Le squelette du Dashboard est en place ! La sidebar permet de naviguer et de revenir au site conventionnel via le lien bleu. C'est ici que nous allons intégrer notre tableau de commandes et le fameux filtre dynamique en Fetch API.
                </p>
            </div>
        </main>

    </div>
</div>