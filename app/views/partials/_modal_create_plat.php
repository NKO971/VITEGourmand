<!-- Modal Création Plat -->
<div class="modal fade" id="modalCreatePlat" tabindex="-1" aria-labelledby="modalCreatePlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCreatePlatLabel">
                    <i class="bi bi-plus-circle me-2"></i>Ajouter un nouveau plat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            
            <form id="formCreatePlat" action="index.php?page=create_plat" method="POST">
                <div class="modal-body">
                    
                    <!-- Nom / Titre du Plat -->
                    <div class="mb-3">
                        <label for="create_titre_plat" class="form-label fw-semibold">Titre du plat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_titre_plat" name="titre_plat" placeholder="Ex : Tarte Tatin maison" required>
                    </div>

                    <!-- Statut Actif -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="create_plat_actif" name="actif" value="1" checked>
                        <label class="form-check-label fw-semibold" for="create_plat_actif">Rendre ce plat immédiatement actif</label>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitCreatePlat">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>