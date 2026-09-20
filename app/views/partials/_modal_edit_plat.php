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
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Les champs marqués <span class="text-danger">*</span> sont obligatoires.</p>
                    <!-- Champ caché ID -->
                    <input type="hidden" name="plat_id" id="edit_plat_id">

                    <!-- Titre du plat -->
                    <div class="mb-3">
                        <label for="edit_titre_plat" class="form-label fw-semibold">Titre du plat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_titre_plat" name="titre_plat" required>
                    </div>

                    <!-- Allergènes (14 allergènes majeurs UE 1169/2011) -->
                    <div class="mb-3">
                        <label for="edit_plat_allergenes" class="form-label fw-semibold">Allergènes</label>
                        <select class="form-select" id="edit_plat_allergenes" name="allergenes[]" multiple size="6">
                            <?php if (!empty($allergenes)): ?>
                                <?php foreach ($allergenes as $allergene): ?>
                                    <option value="<?= $allergene['allergene_id'] ?>"><?= htmlspecialchars($allergene['libelle']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs allergènes.</div>
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