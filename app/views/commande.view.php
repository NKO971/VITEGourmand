<main>
    <div class="container mt-5">
        <h1 class="mb-4">Finaliser votre commande</h1>

        <form action="index.php?page=enregistrer_commande" method="POST">
            <div class="row">

                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Vos Informations de Livraison</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nom / Prénom :</strong> <?= htmlspecialchars($user_data['nom'] . ' ' . $user_data['prenom']) ?></p>
                            <p><strong>Email :</strong> <?= htmlspecialchars($user_data['email']) ?></p>
                            <p><strong>Téléphone :</strong> <?= htmlspecialchars($_SESSION['gsm'] ?? '') ?></p>
                            <p><strong>Adresse de base :</strong> <?= htmlspecialchars($user_data['adresse'] ?? '') ?></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nb_personnes" class="form-label fw-bold">Nombre de personnes (couverts)</label>
                        <input type="number" class="form-control" id="nb_personnes" name="nb_personnes" min="1" value="1" required>
                        <div class="form-text">Indiquez le nombre de convives pour ce menu.</div>
                    </div>

                    <div class="mb-3">
                        <label for="postal_code" class="form-label fw-bold">Confirmez le code postal pour la livraison</label>
                        <input type="text" class="form-control" id="postal_code" name="code_postal" maxlength="5"
                            value="<?= htmlspecialchars($user_data['code_postal'] ?? '') ?>" placeholder="Ex: 33000" required>
                        <div class="form-text">Le calcul des frais de livraison se fera automatiquement.</div>
                    </div>

                    <div class="mb-3">
                        <label for="date_prestation" class="form-label fw-bold">Date de la prestation</label>
                        <input type="date" class="form-control" id="date_prestation" name="date_prestation" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Résumé du Menu</h5>
                        </div>
                        <div class="card-body">
                            <h4 class="text-primary"><?= htmlspecialchars($menu['titre'] ?? $menu['nom_menu'] ?? 'Menu sélectionné') ?></h4>
                            <p class="text-muted">Formule traiteur personnalisée</p>

                            <hr>

                            <input type="hidden" id="prix_menu_hidden" value="<?= $menu['prix_par_personne'] ?? 0 ?>">
                            <input type="hidden" id="min_personnes_hidden" value="<?= $menu['min_personnes'] ?? 1 ?>">

                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total Menu (<span id="affichage_nb_personnes">1</span> pers.) :</span>
                                <span id="prix_menu_total" class="fw-bold">0.00 €</span>
                            </div>

                            <div id="ligne_reduction" class="d-flex justify-content-between mb-2 text-danger d-none">
                                <span>Réduction Julie (10%) :</span>
                                <span id="montant_reduction" class="fw-bold">-0.00 €</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Frais de livraison :</span>
                                <span id="prix_livraison" class="fw-bold">0.00 €</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="h5">Total Général :</span>
                                <span id="prix_total" class="h4 text-success fw-bold">0.00 €</span>
                            </div>

                            <button type="submit" class="btn btn-success w-100 btn-lg shadow">Confirmer et payer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>