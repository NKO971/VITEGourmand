<?php
/** @var array $pendingReviews */
?>
<div class="container my-4 moderation-avis">
    <h1 class="mb-4">Modération des avis clients</h1>

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

    <?php if (empty($pendingReviews)): ?>
        <div class="alert alert-info">
            Aucun avis en attente de modération pour le moment.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($pendingReviews as $review): ?>
                <?php 
                    $reviewId = (string) $review['_id'];
                    $note = (int) ($review['note'] ?? 0);
                ?>
                <div class="col-md-4 mb-4" id="avis-<?= $reviewId ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($review['nom_client'] ?? 'Client') ?></h5>
                                <span class="badge bg-warning text-dark">
                                    <?= str_repeat('★', $note) . str_repeat('☆', 5 - $note) ?> (<?= $note ?>/5)
                                </span>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">
                                Commande #<?= htmlspecialchars((string)($review['commande_id'] ?? 'N/A')) ?> 
                                • <?= htmlspecialchars($review['date_creation'] ?? '') ?>
                            </h6>
                            <p class="card-text mt-3">
                                "<?= htmlspecialchars($review['commentaire'] ?? '') ?>"
                            </p>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-refuser" data-id="<?= $reviewId ?>">
                                Refuser
                            </button>
                            <button type="button" class="btn btn-success btn-sm btn-valider" data-id="<?= $reviewId ?>">
                                Valider &amp; Publier
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>