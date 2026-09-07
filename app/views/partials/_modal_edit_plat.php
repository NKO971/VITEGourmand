<!-- Modal de modification d'un plat -->
<div class="modal fade" id="modalEditPlat" tabindex="-1" aria-labelledby="modalEditPlatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditPlat" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditPlatLabel">Modifier le plat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <!-- Champ caché pour stocker l'ID du plat -->
                    <input type="hidden" name="plat_id" id="edit_plat_id">

                    <div class="mb-3">
                        <label for="edit_titre_plat" class="form-label fw-semibold">Titre du plat</label>
                        <input type="text" class="form-control" id="edit_titre_plat" name="titre_plat" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_photo_plat" class="form-label fw-semibold">Photo du plat (optionnel)</label>
                        <input type="file" class="form-control" id="edit_photo_plat" name="photo" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Formats acceptés : JPG, PNG, WEBP.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSavePlat">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>