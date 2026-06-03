<div class="container-fluid px-4 my-5">
        <div class="row gx-5 justify-content-evenly align-items-center">
            <div class="col-12 col-md-6 col-lg-5 formulaires_connexion">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form id="inscription-form" method="post" action="">
                    <fieldset class="fieldset-connexion p-4 shadow-sm rounded bg-white">
                        <legend class="fw-bold text-primary mb-4">Créer un compte</legend>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3" id="nom-div">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3" id="prenom-div">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3" id="email-div">
                            <label for="email" class="form-label">Adresse Email (Identifiant)</label>
                            <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3" id="gsm-div">
                            <label for="gsm" class="form-label">Numéro de GSM</label>
                            <input type="tel" id="gsm" name="gsm" class="form-control" placeholder="0612345678" required value="<?= htmlspecialchars($_POST['gsm'] ?? '') ?>">
                        </div>

                        <div class="mb-3" id="adresse-div">
                            <label for="adresse" class="form-label">Adresse postale complète</label>
                            <textarea id="adresse" name="adresse" class="form-control" rows="2" required><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3" id="password-div">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="10 caractères min (Maj, min, chiffre, spécial)" required>
                        </div>

                        <div class="mb-4" id="password-confirm-div">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" id="btnInscription" class="btn btn-primary fw-bold">S'inscrire</button>
                        </div>

                        <div class="text-center">
                            <a href="index.php?page=connexion" class="text-decoration-none small">Déjà inscrit ? Se connecter</a>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="col-12 col-md-6 col-lg-5 d-none d-md-flex align-items-center justify-content-center">
                <img src="public/images/register-illustration.png" alt="VITEGourmand Inscription" class="img-fluid image-voiture">
            </div>
        </div>
    </div>
