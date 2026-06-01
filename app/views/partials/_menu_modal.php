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
          <hr class="my-3">
          <h6 class="fw-bold text-warning mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Conditions & Conservation :</h6>
          <ul class="list-unstyled small mb-0">
            <?php if (!empty($conditions['delai_commande'])): ?>
              <li class="mb-1"><strong>Délai de commande :</strong> À réserver au moins <?= htmlspecialchars($conditions['delai_commande']) ?>.</li>
            <?php endif; ?>
            <?php if (!empty($conditions['conservation'])): ?>
              <li><strong>Précautions :</strong> <?= htmlspecialchars($conditions['conservation']) ?></li>
            <?php endif; ?>
          </ul>
        <?php endif; ?>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-light btn-sm text-muted" data-bs-dismiss="modal">Annuler</button>

        <a href="index.php?page=commander&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary">
          <i class="bi bi-cart-plus me-2"></i>Commander ce menu
        </a>
      </div>
    </div>
  </div>
</div>