<body>
    <main>
        <div class="container-fluid px-4">
            <div class="row gx-5 justify-content-evenly">
                <div class="col-12 col-md-6 col-lg-5 formulaires_connexion">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <!-- Formulaire de connexion -->
                    <form id="connexion-form" method="post" action="?page=connexion">
                        <fieldset class="fieldset-connexion">
                            <legend>Connexion</legend>
                            <div class="connexion" id="email-div">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="connexion" id="password-div">
                                <label for="password">Mot de passe</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <div class="connexion">
                                <label class="checkbox">
                                    <input type="checkbox" name="restez-connecte">
                                    Restez connecté
                                </label>
                            </div>
                            <div>
                                <button type="submit" id="btnConnexion" class="btn btn-primary">Connexion</button>
                            </div>
                            <div>
                                <button class="motDePasseOublie" type="button"
                                    onclick="location.href='?page=inscription'">Pas encore inscrit ? S'inscrire</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </main>
  
    <script src="/EcoRide/js/connexion.js" defer></script>
</body>

</html>
