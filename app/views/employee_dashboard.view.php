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

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 bg-light">
    
    <!-- En-tête de la zone de travail -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">Gestion des commandes</h1>
        <div class="text-muted">
            Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></strong>
        </div>
    </div>

    <!-- BARRE DE FILTRES DYNAMIQUES -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="filter-form" onsubmit="event.preventDefault();" class="row g-3 align-items-end">
                
                <!-- Recherche textuelle (Nom client ou N° commande) -->
                <div class="col-md-4">
                    <label for="filter-search" class="form-label text-muted small fw-bold">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Nom, prénom ou n° commande...">
                    </div>
                </div>

                <!-- Filtre par Statut -->
                <div class="col-md-3">
                    <label for="filter-status" class="form-label text-muted small fw-bold">Statut</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours de préparation</option>
                        <option value="prete">Prête</option>
                        <option value="livree">Livrée</option>
                        <option value="annulee">Annulée</option>
                    </select>
                </div>

                <!-- Filtre par Date -->
                <div class="col-md-3">
                    <label for="filter-date" class="form-label text-muted small fw-bold">Date de commande</label>
                    <input type="date" id="filter-date" class="form-control">
                </div>

                <!-- Bouton Réinitialiser -->
                <div class="col-md-2">
                    <button type="button" id="btn-reset" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Réinitialiser
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- TABLEAU DES COMMANDES -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Commandes reçues</h5>
            <span class="badge bg-primary fs-6" id="orders-count">0 commande(s)</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col"># N°</th>
                        <th scope="col">Client</th>
                        <th scope="col">Date / Heure</th>
                        <th scope="col">Montant</th>
                        <th scope="col">Statut</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    <!-- État de chargement initial -->
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Chargement des commandes en cours...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>

    </div>
</div>