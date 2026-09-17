<div class="modal fade" id="menuModal-<?= $menu['menu_id'] ?>" tabindex="-1" aria-labelledby="menuModalLabel-<?= $menu['menu_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="menuModalLabel-<?= $menu['menu_id'] ?>"><?= htmlspecialchars($menu['titre']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">

        <?php
        // Galerie photos du menu (table menu_images), sinon repli sur menu.image
        $galerieMenu = $galeries[$menu['menu_id']] ?? [];
        if (empty($galerieMenu) && !empty($menu['image'])) {
            $galerieMenu = [$menu['image']];
        }
        $carouselId = 'carouselMenu-' . $menu['menu_id'];
        ?>

        <?php if (!empty($galerieMenu)): ?>
          <div id="<?= $carouselId ?>" class="carousel slide mb-4 shadow-sm rounded overflow-hidden" data-bs-ride="false">
            <div class="carousel-inner">
              <?php foreach ($galerieMenu as $i => $imgUrl): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                  <img src="<?= htmlspecialchars($imgUrl) ?>" class="d-block w-100" style="height: 260px; object-fit: cover;" alt="Photo <?= $i + 1 ?> du menu <?= htmlspecialchars($menu['titre']) ?>" loading="lazy">
                </div>
              <?php endforeach; ?>
            </div>
            <?php if (count($galerieMenu) > 1): ?>
              <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
              </button>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <p class="text-muted small fst-italic mb-3">Aucune photo pour ce menu pour le moment.</p>
        <?php endif; ?>

        <?php if (!empty($composition)): ?>
          <h6 class="fw-bold text-primary mb-3">Composition du menu :</h6>

          <div class="mb-3">
            <span class="badge bg-secondary mb-1">Entrée</span>
            <p class="mb-0 fw-semibold"><?= htmlspecialchars($composition['entree']['nom'] ?? 'Non renseignée') ?></p>
            <?php if (!empty($composition['entree']['allergenes'])): ?>
              <?php foreach ($composition['entree']['allergenes'] as $allergene): ?>
                <span class="badge bg-danger-subtle text-danger small">⚠️ <?= htmlspecialchars($allergene) ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <span class="badge bg-secondary mb-1">Plat</span>
            <p class="mb-0 fw-semibold"><?= htmlspecialchars($composition['plat']['nom'] ?? 'Non renseigné') ?></p>
            <?php if (!empty($composition['plat']['allergenes'])): ?>
              <?php foreach ($composition['plat']['allergenes'] as $allergene): ?>
                <span class="badge bg-danger-subtle text-danger small">⚠️ <?= htmlspecialchars($allergene) ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <span class="badge bg-secondary mb-1">Dessert</span>
            <p class="mb-0 fw-semibold"><?= htmlspecialchars($composition['dessert']['nom'] ?? 'Non renseigné') ?></p>
            <?php if (!empty($composition['dessert']['allergenes'])): ?>
              <?php foreach ($composition['dessert']['allergenes'] as $allergene): ?>
                <span class="badge bg-danger-subtle text-danger small">⚠️ <?= htmlspecialchars($allergene) ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        <?php else: ?>
          <p class="text-muted fst-italic">Aucune composition renseignée pour ce menu actuellement.</p>
        <?php endif; ?>

        <?php
        $conditions = json_decode($menu['conditions_stockage'] ?? '[]', true);
        $delaiValeur = $menu['delai_commande_valeur'] ?? null;
        $delaiUnite  = $menu['delai_commande_unite'] ?? null;
        $hasDelai = !empty($delaiValeur) && !empty($delaiUnite);
        ?>

        <?php if ($hasDelai || !empty($conditions)): ?>
          <div class="alert alert-warning border-warning-subtle my-3" role="alert">
            <h6 class="alert-heading fw-bold d-flex align-items-center mb-2 text-warning-emphasis">
              <i class="bi bi-exclamation-triangle-fill me-2 fs-5" aria-hidden="true"></i>
              IMPORTANT : Conditions obligatoires de ce menu
            </h6>
            <hr class="my-2 bg-warning-border">
            <ul class="mb-0 small text-dark">
              <?php if ($hasDelai): ?>
                <li class="mb-1">
                  <strong>Délai de réservation :</strong> Ce menu doit impérativement être commandé au moins <span class="badge bg-dark"><?= (int)$delaiValeur ?> <?= htmlspecialchars($delaiUnite) ?></span> avant la prestation.
                </li>
              <?php endif; ?>
              <?php if (!empty($conditions['conservation'])): ?>
                <li>
                  <strong>Consignes de stockage :</strong> <?= htmlspecialchars($conditions['conservation']) ?>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-light btn-sm text-muted" data-bs-dismiss="modal">Annuler</button>

        <?php if (isset($_SESSION['user_id'])): ?>

          <a href="index.php?page=commander&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-cart-plus me-2" aria-hidden="true"></i>Commander ce menu
          </a>

        <?php else: ?>

          <a href="index.php?page=connexion&redirect_to=commander&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-outline-danger fw-bold">
            <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>Se connecter pour commander
          </a>

        <?php endif; ?>
      </div>
    </div>
  </div>
</div>