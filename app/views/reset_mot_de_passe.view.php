<main>
    <div class="container my-5 py-4">
        <div class="row gx-5 justify-content-evenly align-items-center">

            <div class="col-12 col-md-6 col-lg-5 mb-5 mb-md-0">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success shadow-sm border-0"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if (empty($success)): ?>
                <form method="post" action="?page=reset-mot-de-passe">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <fieldset class="fieldset-connexion p-4 bg-white">
                        <legend class="fw-bold mb-4">Nouveau mot de passe</legend>
                        <p class="text-muted small mb-3">Les champs marqués <span class="text-danger">*</span> sont obligatoires.</p>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-medium text-secondary">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                            <div class="form-text">10 caractères min., 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label small fw-medium text-secondary">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-vg-primary w-100 py-2.5 fw-bold">
                                Réinitialiser mon mot de passe
                            </button>
                        </div>
                    </fieldset>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>