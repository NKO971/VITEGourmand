<!-- En-tête de la zone de travail -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">Gestion des commandes</h1>
    <div class="text-muted">
        Connecté en tant que : <strong><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></strong>
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