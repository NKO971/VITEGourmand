<main>
    <div class="container mt-5">
        <?php if (!empty($_SESSION['flash_message'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        <h1 class="mb-4">Modifier ma commande</h1>

        <form action="index.php?page=update_commande" method="POST">
            <input type="hidden" name="commande_id" value="<?= htmlspecialchars($commande['commande_id']) ?>">
            <div class="row">

                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Vos Informations de Livraison</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nom / Prénom :</strong> <?= htmlspecialchars($_SESSION['nom'] . ' ' . $_SESSION['prenom']) ?></p>
                            <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
                            <p><strong>Téléphone :</strong> <?= htmlspecialchars($_SESSION['gsm'] ?? '') ?></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nb_personnes" class="form-label fw-bold">Nombre de personnes (couverts)</label>
                        <input type="number" class="form-control" id="nb_personnes" name="nb_personnes" min="1"
                            value="<?= htmlspecialchars($commande['nombre_personne']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="heure_livraison" class="form-label fw-bold">Heure souhaitée</label>
                        <input type="time" class="form-control" name="heure_livraison"
                            value="<?= htmlspecialchars($commande['heure_livraison']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="lieu_livraison" class="form-label fw-bold">Lieu de livraison</label>
                        <input type="text" class="form-control" name="lieu_livraison"
                            value="<?= htmlspecialchars($commande['adresse_livraison']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="postal_code" class="form-label fw-bold">Confirmez le code postal pour la livraison</label>
                        <input type="text" class="form-control" id="postal_code" name="code_postal" maxlength="5"
                            value="<?= htmlspecialchars($commande['code_postal_livraison']) ?>" placeholder="Ex: 33000" required>
                        <div class="form-text">Le calcul des frais de livraison se fera automatiquement.</div>
                    </div>

                    <div class="mb-3">
                        <label for="date_prestation" class="form-label fw-bold">Date de la prestation</label>
                        <input type="date" class="form-control" id="date_prestation" name="date_prestation"
                            value="<?= htmlspecialchars($commande['date_prestation']) ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Résumé du Menu (non modifiable)</h5>
                        </div>
                        <div class="card-body">
                            <h4 class="text-primary"><?= htmlspecialchars($menu['titre'] ?? 'Menu sélectionné') ?></h4>
                            <p class="text-muted">Le choix du menu ne peut pas être modifié pour cette commande.</p>

                            <hr>

                            <input type="hidden" id="prix_menu_hidden" value="<?= $menu['prix_par_personne'] ?? 0 ?>">
                            <input type="hidden" id="min_personnes_hidden" value="<?= $menu['nombre_personne_minimum'] ?? 1 ?>">

                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total Menu (<span id="affichage_nb_personnes">1</span> pers.) :</span>
                                <span id="prix_menu_total" class="fw-bold">0.00 €</span>
                            </div>

                            <div id="ligne_reduction" class="d-flex justify-content-between mb-2 text-danger d-none">
                                <span>Réduction (10%) :</span>
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

                            <button type="submit" class="btn btn-success w-100 btn-lg shadow">Confirmer la modification</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>