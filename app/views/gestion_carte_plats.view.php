<div class="container-fluid my-4 moderation-menus-plats">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark fw-bold mb-0">Gestion de la Carte (Menus & Plats)</h1>
    </div>

    <!-- Message de notification Flash si présent en session -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- SECTION 1 : GESTION DES MENUS -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title text-primary mb-0"><i class="bi bi-egg-fried me-2"></i>Liste des Menus</h5>
            <button class="btn btn-sm btn-outline-primary" id="btn-add-menu"><i class="bi bi-plus-circle me-1"></i> Ajouter menu</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Prix</th>
                            <th>Thème / Régime</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($menus)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Aucun menu enregistré.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($menus as $menu): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars((string)$menu['menu_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($menu['titre'] ?? '') ?></td>
                                    <td><?= number_format((float)($menu['prix_par_personne'] ?? $menu['prix'] ?? 0), 2, ',', ' ') ?> €</td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($menu['theme_libelle'] ?? 'N/A') ?></span>
                                        <span class="badge bg-outline-dark text-dark border"><?= htmlspecialchars($menu['regime_libelle'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status <?= ($menu['actif'] ?? 1) == 1 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ($menu['actif'] ?? 1) == 1 ? 'Actif' : 'Masqué' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                        $rawComp = $menu['composition'] ?? '{}';
                                        $jsonComposition = is_array($rawComp) ? json_encode($rawComp, JSON_UNESCAPED_UNICODE) : $rawComp;
                                        $rawCond = $menu['conditions_stockage'] ?? '{}';
                                        $jsonConditions = is_array($rawCond) ? json_encode($rawCond, JSON_UNESCAPED_UNICODE) : $rawCond;
                                        ?>
                                        <button class="btn btn-sm btn-outline-primary me-1 btn-edit-menu"
                                            data-id="<?= $menu['menu_id'] ?>"
                                            data-titre="<?= htmlspecialchars($menu['titre'] ?? '', ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($menu['description'] ?? '', ENT_QUOTES) ?>"
                                            data-prix="<?= $menu['prix_par_personne'] ?? $menu['prix'] ?? 0 ?>"
                                            data-stock="<?= $menu['quantite_restante'] ?? $menu['stock'] ?? 0 ?>"
                                            data-theme="<?= $menu['theme_id'] ?? '' ?>"
                                            data-regime="<?= $menu['regime_id'] ?? '' ?>"
                                            data-composition='<?= htmlspecialchars($jsonComposition, ENT_QUOTES, 'UTF-8') ?>'
                                            data-conditions='<?= htmlspecialchars($jsonConditions, ENT_QUOTES, 'UTF-8') ?>'>
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <button class="btn btn-sm <?= ($menu['actif'] ?? 1) == 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-toggle-menu"
                                            data-id="<?= $menu['menu_id'] ?>"
                                            data-actif="<?= ($menu['actif'] ?? 1) == 1 ? 0 : 1 ?>">
                                            <i class="bi <?= ($menu['actif'] ?? 1) == 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                            <?= ($menu['actif'] ?? 1) == 1 ? 'Masquer' : 'Activer' ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2 : GESTION DES PLATS -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title text-primary mb-0"><i class="bi bi-disc me-2"></i>Liste des Plats</h5>
            <button class="btn btn-sm btn-outline-primary" id="btn-add-plat"><i class="bi bi-plus-circle me-1"></i> Ajouter un plat</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre du plat</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plats)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Aucun plat enregistré.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plats as $plat): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars((string)$plat['plat_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($plat['titre_plat'] ?? '') ?></td>
                                    <td>
                                        <span class="badge badge-status <?= ($plat['actif'] ?? 1) == 1 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ($plat['actif'] ?? 1) == 1 ? 'Actif' : 'Masqué' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1 btn-edit-plat" data-id="<?= $plat['plat_id'] ?>">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <button class="btn btn-sm <?= ($plat['actif'] ?? 1) == 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-toggle-plat"
                                            data-id="<?= $plat['plat_id'] ?>"
                                            data-actif="<?= ($plat['actif'] ?? 1) == 1 ? 0 : 1 ?>">
                                            <i class="bi <?= ($plat['actif'] ?? 1) == 1 ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                            <?= ($plat['actif'] ?? 1) == 1 ? 'Masquer' : 'Activer' ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once ROOT_PATH . 'app/views/partials/_modal_edit_plat.php'; ?>
    <?php require_once ROOT_PATH . 'app/views/partials/_modal_edit_menu.php'; ?>
    <?php require_once ROOT_PATH . 'app/views/partials/_modal_create_plat.php'; ?>
</div>