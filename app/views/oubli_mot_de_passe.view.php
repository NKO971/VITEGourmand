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
                <form method="post" action="?page=oubli-mot-de-passe">
                    <fieldset class="fieldset-connexion p-4 bg-white">
                        <legend class="fw-bold mb-4">Mot de passe oublié</legend>
                        <p class="text-muted small mb-3">Les champs marqués <span class="text-danger">*</span> sont obligatoires.</p>

                        <p class="text-muted small mb-4">Renseignez votre adresse email, vous recevrez un lien pour réinitialiser votre mot de passe.</p>

                        <div class="mb-4" id="email-div">
                            <label for="email" class="form-label small fw-medium text-secondary">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-vg-primary w-100 py-2.5 fw-bold">
                                Envoyer le lien de réinitialisation
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="?page=connexion" class="small">Retour à la connexion</a>
                        </div>
                    </fieldset>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>