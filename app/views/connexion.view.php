<main>
    <div class="container my-5 py-4">
        <div class="row gx-5 justify-content-evenly align-items-center">

            <div class="col-12 col-md-6 col-lg-5 mb-5 mb-md-0">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form id="connexion-form" method="post" action="">
                    <fieldset class="fieldset-connexion p-4 bg-white">
                        <legend class="fw-bold mb-4">Connexion</legend>

                        <div class="mb-3" id="email-div">
                            <label for="email" class="form-label small fw-medium text-secondary">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2" id="password-div">
                            <label for="password" class="form-label small fw-medium text-secondary">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-4 text-end">
                            <a href="/VITEGourmand/public/?page=oubli-mot-de-passe" class="small text-muted">Mot de passe oublié ?</a>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="restez-connecte" name="restez-connecte">
                            <label class="form-check-label small text-secondary" for="restez-connecte">Restez connecté</label>
                        </div>

                        <div class="mb-4">
                            <button type="submit" id="btnConnexion" class="btn btn-vg-primary w-100 py-2.5 fw-bold">
                                Se connecter
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="/VITEGourmand/public/?page=inscription" class="small">Pas encore inscrit ? S'inscrire</a>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="col-12 col-md-6 col-lg-5 text-center text-md-start d-flex flex-column align-items-center align-items-md-start">
                <div class="vg-welcome-badge mb-3">
                    <i class="bi bi-fire"></i> Content de vous revoir !
                </div>
                <h2 class="vg-title-inscription fw-black mb-3 text-center text-md-start">
                    Ravi de vous <br><span class="text-dark d-block d-md-inline">revoir à table</span>
                </h2>
                <p class="text-muted max-w-300 small text-center text-md-start">
                    Connectez-vous pour retrouver vos favoris, suivre vos commandes en cours et découvrir les suggestions du jour.
                </p>
            </div>

        </div>
    </div>
</main>