<!-- Modal Création Employé -->
<div class="modal fade" id="modalCreateEmploye" tabindex="-1" aria-labelledby="modalCreateEmployeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCreateEmployeLabel"><i class="bi bi-person-plus me-2"></i>Créer un compte employé</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="formCreateEmploye">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_employe_email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="create_employe_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_employe_password" class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="create_employe_password" name="password" required>
                        <div class="form-text">10 caractères min., majuscule, minuscule, chiffre, caractère spécial.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnCreateEmploye">Créer le compte</button>
                </div>
            </form>
        </div>
    </div>
</div>