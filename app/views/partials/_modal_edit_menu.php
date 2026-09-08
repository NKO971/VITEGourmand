<!-- Modal Modification Menu -->
<div class="modal fade" id="modalEditMenu" tabindex="-1" aria-labelledby="modalEditMenuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditMenu">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditMenuLabel"><i class="bi bi-pencil-square me-2"></i>Modifier le menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <!-- Champ caché pour l'ID du menu -->
                    <input type="hidden" name="menu_id" id="edit_menu_id">

                    <!-- Information Générale -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit_menu_titre" class="form-label fw-semibold">Titre du menu</label>
                            <input type="text" class="form-control" id="edit_menu_titre" name="titre" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_menu_prix" class="form-label fw-semibold">Prix (€)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_menu_prix" name="prix" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_menu_stock" class="form-label fw-semibold">Stock disponible</label>
                            <input type="number" min="0" class="form-control" id="edit_menu_stock" name="stock" required>
                        </div>
                    </div>

                    <!-- Thème et Régime -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="edit_menu_theme" class="form-label fw-semibold">Thème</label>
                            <select class="form-select" id="edit_menu_theme" name="theme_id" required>
                                <option value="">-- Sélectionner un thème --</option>
                                <?php if (!empty($themes)): ?>
                                    <?php foreach ($themes as $theme): ?>
                                        <option value="<?= $theme['theme_id'] ?>"><?= htmlspecialchars($theme['libelle']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_menu_regime" class="form-label fw-semibold">Régime alimentaire</label>
                            <select class="form-select" id="edit_menu_regime" name="regime_id" required>
                                <option value="">-- Sélectionner un régime --</option>
                                <?php if (!empty($regimes)): ?>
                                    <?php foreach ($regimes as $regime): ?>
                                        <option value="<?= $regime['regime_id'] ?>"><?= htmlspecialchars($regime['libelle']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Description du menu -->
                    <div class="mb-3">
                        <label for="edit_menu_description" class="form-label fw-semibold">Description du menu</label>
                        <textarea class="form-control" id="edit_menu_description" name="description" rows="3" placeholder="Saisissez une description attractive pour ce menu..."></textarea>
                    </div>

                    <hr class="my-4">

                    <!-- Composition du Menu (Catalogue des plats actifs) -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-journal-text me-2"></i>Composition du menu</h6>

                        <div class="row g-3">
                            <!-- Entrée -->
                            <div class="col-md-4">
                                <label for="edit_menu_entree" class="form-label fw-semibold">Entrée</label>
                                <select class="form-select select-plat-composition" id="edit_menu_entree" data-type="entree">
                                    <option value="">-- Aucune entrée --</option>
                                    <?php if (!empty($plats)): ?>
                                        <?php foreach ($plats as $plat): ?>
                                            <?php if (($plat['actif'] ?? 1) == 1): ?>
                                                <option value="<?= $plat['plat_id'] ?>" data-nom="<?= htmlspecialchars($plat['titre_plat']) ?>">
                                                    <?= htmlspecialchars($plat['titre_plat']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Plat Principal -->
                            <div class="col-md-4">
                                <label for="edit_menu_plat_principal" class="form-label fw-semibold">Plat principal</label>
                                <select class="form-select select-plat-composition" id="edit_menu_plat_principal" data-type="plat" required>
                                    <option value="">-- Choisir un plat --</option>
                                    <?php if (!empty($plats)): ?>
                                        <?php foreach ($plats as $plat): ?>
                                            <?php if (($plat['actif'] ?? 1) == 1): ?>
                                                <option value="<?= $plat['plat_id'] ?>" data-nom="<?= htmlspecialchars($plat['titre_plat']) ?>">
                                                    <?= htmlspecialchars($plat['titre_plat']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Dessert -->
                            <div class="col-md-4">
                                <label for="edit_menu_dessert" class="form-label fw-semibold">Dessert</label>
                                <select class="form-select select-plat-composition" id="edit_menu_dessert" data-type="dessert">
                                    <option value="">-- Aucun dessert --</option>
                                    <?php if (!empty($plats)): ?>
                                        <?php foreach ($plats as $plat): ?>
                                            <?php if (($plat['actif'] ?? 1) == 1): ?>
                                                <option value="<?= $plat['plat_id'] ?>" data-nom="<?= htmlspecialchars($plat['titre_plat']) ?>">
                                                    <?= htmlspecialchars($plat['titre_plat']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Conditions de Stockage & Livraison -->
                    <div>
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-box-seam me-2"></i>Conditions de stockage & conservation</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_delai_commande" class="form-label fw-semibold">Délai de commande minimum</label>
                                <input type="text" class="form-control" id="edit_delai_commande" placeholder="ex : 24h à l'avance" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_conservation" class="form-label fw-semibold">Instruction de conservation</label>
                                <input type="text" class="form-control" id="edit_conservation" placeholder="ex : À conserver entre 0°C et 4°C" required>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveMenu">Enregistrer le menu</button>
                </div>
            </form>
        </div>
    </div>
</div>