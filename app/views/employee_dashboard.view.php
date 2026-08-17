<!-- En-tête de la zone de travail -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">Gestion des commandes</h1>
    <div class="text-muted">
        Connecté en tant que : <strong><?= htmlspecialchars(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></strong>
    </div>
</div>

<!-- COMMANDES EN ATTENTE DE VALIDATION -->
<div id="pending-orders-container" class="mb-5 d-none">
    <div class="card border-warning shadow-sm">
        <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Nouvelles commandes à traiter
            </h5>
            <span class="badge bg-dark text-white fs-6" id="pending-count">0 en attente</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col"># N°</th>
                        <th scope="col">Client</th>
                        <th scope="col">Date / Heure</th>
                        <th scope="col">Montant</th>
                        <th scope="col" class="text-end">Actions requises</th>
                    </tr>
                </thead>
                <tbody id="pending-orders-tbody">
                    <!-- Généré dynamiquement en JS -->
                </tbody>
            </table>
        </div>
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
            <!-- Filtre par Statut -->
            <div class="col-md-3">
                <label for="filter-status" class="form-label text-muted small fw-bold">Statut</label>
                <select id="filter-status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="Acceptée">Acceptée</option>
                    <option value="En préparation">En préparation</option>
                    <option value="En cours de livraison">En cours de livraison</option>
                    <option value="En attente du retour de matériel">En attente du retour de matériel</option>
                    <option value="Terminée">Terminée</option>
                    <option value="Annulée">Annulée</option>
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

<!-- Modal de détail de commande -->
<!-- MODALE DETAIL ET MODIFICATION COMMANDE -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <!-- En-tête de la modale -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="orderModalLabel">
                    Détails de la commande <span id="modal-order-number" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <!-- Formulaire de modification -->
            <form id="form-update-order">
                <div class="modal-body">
                    <input type="hidden" id="modal-order-id" name="commande_id">

                    <!-- Infos Client & Prestation -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-1">CLIENT</h6>
                            <p id="modal-client-info" class="fw-bold fs-6 mb-0"></p>
                            <small id="modal-client-email" class="text-primary"></small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="fw-bold text-muted mb-1">DÉTAILS PRESTATION</h6>
                            <p id="modal-order-date" class="fw-bold mb-0"></p>
                            <small id="modal-order-guests" class="text-muted"></small>
                        </div>
                    </div>

                    <!-- BLOC DYNAMIQUE : Alertes Matériel Prêté & CGV 600 € -->
                    <div id="modal-equipment-alert" class="alert alert-warning border-warning d-flex align-items-start mb-3 d-none" role="alert">
                        <i class="bi bi-box-seam fs-4 me-3 text-warning-emphasis"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1" id="equipment-alert-title">Prêt de matériel associé</h6>
                            <p class="mb-0 small" id="equipment-alert-desc">
                                Du matériel a été mis à disposition du client pour cette prestation.
                            </p>
                            <div id="equipment-penalty-notice" class="mt-2 pt-2 border-top border-warning-subtle small text-danger">
                                <div class="fw-bold mb-1 text-dark">
                                    <i class="bi bi-calendar-check me-1 text-warning-emphasis"></i> Date limite de remise :
                                    <span id="modal-equipment-deadline" class="badge bg-warning text-dark fs-6 ms-1"></span>
                                </div>
                                <div class="fw-bold">
                                    <i class="bi bi-shield-exclamation me-1"></i> Pénalité non-restitution : <strong>600,00 €</strong> (selon CGV).
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Modification du Statut -->
                    <div class="mb-3">
                        <label for="modal-status-select" class="form-label fw-bold">Changer le statut de la commande :</label>
                        <select class="form-select" id="modal-status-select" name="statut" required>
                            <option value="Acceptée">Acceptée</option>
                            <option value="En préparation">En préparation</option>
                            <option value="En cours de livraison">En cours de livraison</option>
                            <option value="Livré">Livré</option>
                            <option value="En attente du retour de matériel">En attente du retour de matériel</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Annulée">Annulée</option>
                        </select>
                    </div>

                    <!-- Champs obligatoires si Annulation -->
                    <div id="cancellation-fields" class="p-3 bg-light border border-danger rounded d-none">
                        <h6 class="text-danger fw-bold mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Contact client obligatoire avant annulation
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mode de contact utilisé * :</label>
                            <select class="form-select" id="modal-contact-mode" name="mode_contact">
                                <option value="">-- Sélectionner le moyen de contact --</option>
                                <option value="GSM">Appel GSM</option>
                                <option value="Mail">E-mail</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motif de l'annulation * :</label>
                            <textarea class="form-control" id="modal-cancel-reason" name="motif_annulation" rows="3" placeholder="Ex: Rupture de stock / Demande du client par téléphone..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pied de la modale -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>

        </div>
    </div>
</div>