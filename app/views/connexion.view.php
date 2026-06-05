<main>
    <div class="container-fluid px-4 my-5">
        <div class="row gx-5 justify-content-evenly">
            <div class="col-12 col-md-6 col-lg-5 formulaires_connexion">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form id="connexion-form" method="post" action="">
                    <fieldset class="fieldset-connexion p-4 shadow-sm rounded bg-white">
                        <legend class="fw-bold text-primary mb-4">Connexion</legend>
                        
                        <div class="mb-3" id="email-div">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3" id="password-div">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3 text-end">
                            <a href="index.php?page=oubli-mot-de-passe" class="text-decoration-none small text-muted">Mot de passe oublié ?</a>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="restez-connecte" name="restez-connecte">
                            <label class="form-check-label" for="restez-connecte">Restez connecté</label>
                        </div>
                        
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" id="btnConnexion" class="btn btn-primary fw-bold">Connexion</button>
                        </div>
                        
                        <div class="text-center">
                            <a href="index.php?page=inscription" class="text-decoration-none small">Pas encore inscrit ? S'inscrire</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</main>
  <script src="/VITEGourmand/public/js/connexion.js" defer></script>

