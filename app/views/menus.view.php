<!-- Filtres des menus -->
<section id="menus" class="container my-5">
  <h1 class="visually-hidden">Découvrez nos menus</h1>
  <div class="vg-filter-container p-3 rounded shadow-sm border mb-5">
    <h2 class="h6 mb-3 vg-filter-title fw-bold text-uppercase">Filtres de recherche</h2>

    <form class="row g-2 align-items-center">

      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small" for="prix-min">Prix Min (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-min" placeholder="0">
          </div>
          <div class="col-6">
            <label class="form-label small" for="prix-max">Prix Max (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-max" placeholder="100">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small" for="filtre-convives">Convives</label>
            <select class="form-select form-select-sm" id="filtre-convives">
              <option value="Tous">Tous</option>
              <option value="2">2 personnes</option>
              <option value="4">4 personnes</option>
              <option value="6">6 personnes et +</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small" for="filtre-regime">Régime</label>
            <select class="form-select form-select-sm" id="filtre-regime">
              <option value="tous">Tous</option>
              <?php foreach ($regimes as $regime): ?>
                <option value="<?php echo $regime['regime_id']; ?>"><?php echo $regime['libelle']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="col-12 mt-2">
        <div class="row g-2 align-items-end">
          <div class="col-md-9">
            <label class="form-label small" for="filtre-theme">Thème du menu</label>
            <select class="form-select form-select-sm" id="filtre-theme">
              <option value="tous">Tous</option>
              <?php foreach ($themes as $theme): ?>
                <option value="<?= $theme['theme_id'] ?>"><?= $theme['libelle'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-vg-reset btn-sm w-100" id="bouton-reinitialiser">Réinitialiser</button>
          </div>
        </div>
      </div>

    </form>
  </div>

  <div id="vignettes-container" class="row g-4"></div>
</section>
<!-- Fin des filtres -->

<!-- Sections des menus -->
<section id="menu-categories" class="container my-5">
  <div class="row g-4">

    <?php foreach ($menus as $menu):
      $composition = json_decode($menu['composition'] ?? '[]', true);
      // Vignette carte : menu.image si renseignée, sinon 1re photo galerie, sinon placeholder
      $vignetteMenu = !empty($menu['image']) ? $menu['image'] : (!empty($galeries[$menu['menu_id']][0]) ? $galeries[$menu['menu_id']][0] : 'Image/photo_accueil.jpg');
    ?>
      <div class="col-12 col-md-6 col-lg-4 menu-item-card"
        data-prix="<?php echo $menu['prix_par_personne']; ?>"
        data-theme="<?php echo $menu['theme_id']; ?>"
        data-regime="<?php echo $menu['regime_id']; ?>"
        data-convives="<?php echo $menu['nombre_personne_minimum']; ?>">

        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
          <div class="position-relative">
            <img src="<?= htmlspecialchars($vignetteMenu) ?>" class="card-img-top" alt="Photo du menu <?= htmlspecialchars($menu['titre']) ?>" style="height: 200px; object-fit: cover;">
            <span class="badge vg-badge-stock position-absolute top-0 end-0 m-2">
              Stock : <?php echo $menu['quantite_restante']; ?>
            </span>
          </div>

          <div class="card-body">
            <h3 class="card-title fw-bold"><?php echo htmlspecialchars($menu['titre']); ?></h3>

            <p class="text-muted small mb-2"><i class="bi bi-tag" aria-hidden="true"></i> Thème : <?php echo htmlspecialchars($menu['theme_libelle']); ?></p>

            <div class="d-flex gap-2 mb-3">
              <span class="badge bg-success-subtle text-success"><?php echo htmlspecialchars($menu['regime_libelle']); ?></span>
              <span class="badge bg-info-subtle text-info">Min. <?php echo $menu['nombre_personne_minimum']; ?> pers</span>
            </div>

            <div class="menu-description-container">
              <p class="card-text small text-muted menu-description-text">
                <?= htmlspecialchars($menu['description'] ?? 'Découvrez notre délicieux menu préparé avec soin.') ?>
              </p>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="h5 mb-0 vg-price fw-bold"><?php echo $menu['prix_par_personne']; ?>€</span>
              <button class="btn btn-vg-details btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#menuModal-<?= $menu['menu_id'] ?>">Voir détails</button>
            </div>
          </div>
        </div>
      </div>
      <?php include __DIR__ . '/partials/_menu_modal.php'; ?>
    <?php endforeach; ?>

  </div>
</section>

