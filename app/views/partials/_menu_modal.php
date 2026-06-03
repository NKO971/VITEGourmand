<div class="modal fade" id="menuModal-<?= $menu['menu_id'] ?>" tabindex="-1" aria-labelledby="menuModalLabel-<?= $menu['menu_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="menuModalLabel-<?= $menu['menu_id'] ?>"><?= htmlspecialchars($menu['titre']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

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
        $conditions = json_decode($menu['conditions_stockage'], true);
        ?>

        <?php if (!empty($conditions)): ?>
          <div class="alert alert-warning border-warning-subtle my-3" role="alert">
            <h6 class="alert-heading fw-bold d-flex align-items-center mb-2 text-warning-emphasis">
              <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
              IMPORTANT : Conditions obligatoires de ce menu
            </h6>
            <hr class="my-2 bg-warning-border">
            <ul class="mb-0 small text-dark">
              <?php if (!empty($conditions['delai_commande'])): ?>
                <li class="mb-1">
                  <strong>Délai de réservation :</strong> Ce menu doit impérativement être commandé au moins <span class="badge bg-dark"><?= htmlspecialchars($conditions['delai_commande']) ?></span> avant la prestation.
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

        <?php if (isset($_SESSION['user'])): ?>
          <a href="index.php?page=commander&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-cart-plus me-2"></i>Commander ce menu
          </a>
        <?php else: ?>
          <a href="index.php?page=connexion&redirect_to=commander&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-outline-danger fw-bold">
            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter pour commander
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>