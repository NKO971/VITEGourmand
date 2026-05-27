
<body>
    <main>
        <!-- Profil utilisateur -->
    <?php require_once __DIR__ . '/partials/-form-profil.php'; ?>
        <!-- Fin du profil utilisateur -->

        <!-- Choix du rôle -->
    <section class="choix-role">
            <legend>Mon statut sur la plateforme</legend>
            <p class="role-instruction">Je choisis mon rôle sur EcoRide :</p>

            <div class="roles d-flex">
                <div class="form-check role-card">
                    <input class="form-check-input" type="radio" name="role_preference" id="passager" value="passager"
                        checked>
                    <label class="form-check-label" for="passager">
                        <strong>Passager</strong><br>
                        <small>Je cherche des trajets</small>
                    </label>
                </div>

                <div class="form-check role-card">
                    <input class="form-check-input" type="radio" name="role_preference" id="chauffeur"
                        value="chauffeur">
                    <label class="form-check-label" for="chauffeur">
                        <strong>Chauffeur</strong><br>
                        <small>Je propose mes services</small>
                    </label>
                </div>

                <div class="form-check role-card">
                    <input class="form-check-input" type="radio" name="role_preference" id="les_deux" value="les_deux">
                    <label class="form-check-label" for="les_deux">
                        <strong>Les deux</strong><br>
                        <small>Je voyage et je conduis</small>
                    </label>
                </div>
            </div>

            <div class="action-passager mt-4 text-center">
               <a href="?page=covoiturage" class="btn-recherche-trajet ">
               Prêt à partir ? 
               </a>
            </div>
    </section>
        <!-- Fin du choix du rôle -->

        <div class="conteneur-chauffeur-flex">
            <!-- Formulaire véhicule -->
<?php require_once __DIR__ . '/partials/_form_vehicule.php'; ?>
            <!-- Formulaire trajet -->
<?php require_once __DIR__ . '/partials/_form_trajet.php'; ?>
        </div>
<?php require_once __DIR__ . '/partials/_historique_trajets.php'; ?>       
    </main>
</body>

</html>
