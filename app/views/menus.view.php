<!-- Filtres des menus -->
<section id="menus" class="container my-5">
  <div class="vg-filter-container p-3 rounded shadow-sm border mb-5">
    <h3 class="h6 mb-3 vg-filter-title fw-bold text-uppercase">Filtres de recherche</h3>

    <form class="row g-2 align-items-center">

      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small">Prix Min (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-min" placeholder="0">
          </div>
          <div class="col-6">
            <label class="form-label small">Prix Max (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-max" placeholder="100">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small">Convives</label>
            <select class="form-select form-select-sm" id="filtre-convives">
              <option value="Tous">Tous</option>
              <option value="2">2 personnes</option>
              <option value="4">4 personnes</option>
              <option value="6">6 personnes et +</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Régime</label>
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
            <label class="form-label small">Thème du menu</label>
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
    
    <?php foreach ($menus as $menu): ?>
      <div class="col-12 col-md-6 col-lg-4 menu-item-card" 
           data-prix="<?php echo $menu['prix_par_personne']; ?>"
           data-theme="<?php echo $menu['theme_id']; ?>"
           data-regime="<?php echo $menu['regime_id']; ?>"
           data-convives="<?php echo $menu['nombre_personne_minimum']; ?>">
        
        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
          <div class="position-relative">
            <img src="Image/photo_accueil.jpg" class="card-img-top" alt="Menu" style="height: 200px; object-fit: cover;">
            <span class="badge vg-badge-stock position-absolute top-0 end-0 m-2">
              Stock : <?php echo $menu['quantite_restante']; ?>
            </span>
          </div>

          <div class="card-body">
            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($menu['titre']); ?></h5>
            
            <p class="text-muted small mb-2"><i class="bi bi-tag"></i> Thème : <?php echo htmlspecialchars($menu['theme_libelle']); ?></p>

            <div class="d-flex gap-2 mb-3">
              <span class="badge bg-success-subtle text-success"><?php echo htmlspecialchars($menu['regime_libelle']); ?></span>
              <span class="badge bg-info-subtle text-info">Min. <?php echo $menu['nombre_personne_minimum']; ?> pers</span>
            </div>

            <p class="card-text small text-truncate">Découvrez notre délicieux menu préparé avec soin.</p>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="h5 mb-0 vg-price fw-bold"><?php echo $menu['prix_par_personne']; ?>€</span>
              <button class="btn btn-vg-details btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">Voir détails</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</section>

<script src="JS/menus-filter.js"></script>