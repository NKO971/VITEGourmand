<!-- Modal Modification Plat -->
<div class="modal fade" id="modalEditPlat" tabindex="-1" aria-labelledby="modalEditPlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalEditPlatLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modifier le plat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            
            <form id="formEditPlat" action="index.php?action=update_plat" method="POST">
                <div class="modal-body">
                    <!-- Champ caché ID -->
                    <input type="hidden" name="plat_id" id="edit_plat_id">

                    <!-- Titre du plat -->
                    <div class="mb-3">
                        <label for="edit_titre_plat" class="form-label fw-semibold">Titre du plat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_titre_plat" name="titre_plat" required>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSavePlat">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>