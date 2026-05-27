<section class="historique">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="px-4 py-3 bg-light border-bottom">
                <ul class="nav nav-pills gap-3" id="historique-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active small fw-bold text-uppercase border-0 p-0 bg-transparent text-primary"
                            id="tab-avenir" data-bs-toggle="pill" data-bs-target="#liste-avenir" type="button" role="tab">
                            À venir (<?php echo count($trajetsAvenir); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small fw-bold text-uppercase border-0 p-0 bg-transparent text-warning"
                            id="tab-en-cours" data-bs-toggle="pill" data-bs-target="#liste-en-cours" type="button" role="tab">
                            En cours (<?php echo count($trajetsEnCours); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small fw-bold text-uppercase border-0 p-0 bg-transparent text-muted opacity-50"
                            id="tab-historique" data-bs-toggle="pill" data-bs-target="#liste-historique" type="button" role="tab">
                            Historique (<?php echo count($trajetsPasses); ?>)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="historique-tabContent">
                
                <div class="tab-pane fade show active" id="liste-avenir" role="tabpanel">
                    <div class="list-group list-group-flush">
                        <?php if (empty($trajetsAvenir)) : ?>
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">Aucun trajet prévu pour le moment.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($trajetsAvenir as $trajet) : ?>
                                <div class="list-group-item p-4">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h5 class="mb-1 text-primary fw-bold">
                                            <?php echo htmlspecialchars($trajet['lieu_depart']); ?> ➡️ <?php echo htmlspecialchars($trajet['lieu_arrivee']); ?>
                                        </h5>
                                        <small class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <?php echo htmlspecialchars($trajet['statut']); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <div>
                                            Le <strong><?php echo date('d/m/Y', strtotime($trajet['date_depart'])); ?></strong> 
                                            à <strong><?php echo date('H\hi', strtotime($trajet['heure_depart'])); ?></strong>
                                        </div>
                                        <div>
                                            <strong><?php echo (int)$trajet['nb_place']; ?></strong> places dispo. 
                                            <strong><?php echo htmlspecialchars($trajet['prix_personne']); ?></strong> crédits
                                        </div>
                                    </div>

                                    <?php $isChauffeur = !isset($trajet['reservation_id']); ?>

                                    <div class="mt-3 d-flex justify-content-end gap-2">
                                        <?php if ($trajet['statut'] === 'ouvert') : ?>
                                            <?php if ($isChauffeur) : ?>
                                                <a href="?page=profile&action=demarrer-trajet&id=<?php echo $trajet['covoiturage_id']; ?>" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                                                     Démarrer le covoiturage
                                                </a>
                                                <a href="?page=profile&action=annuler-trajet&id=<?php echo $trajet['covoiturage_id']; ?>" class="btn btn-sm btn-danger fw-bold px-3 shadow-sm">
                                                    Annuler le covoiturage
                                                </a>
                                            <?php else : ?>
                                                <a href="?page=profile&action=annuler-reservation&id=<?php echo $trajet['reservation_id']; ?>" class="btn btn-sm btn-danger fw-bold px-3 shadow-sm text-dark">
                                                    Annuler participation
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="liste-en-cours" role="tabpanel">
                    <div class="list-group list-group-flush">
                        <?php if (empty($trajetsEnCours)) : ?>
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">Aucun covoiturage en cours de route.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($trajetsEnCours as $trajet) : ?>
                                <div class="list-group-item p-4 bg-warning bg-opacity-10 border-warning border-start border-4">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h5 class="mb-1 text-warning-emphasis fw-bold">
                                            <?php echo htmlspecialchars($trajet['lieu_depart']); ?> ➡️ <?php echo htmlspecialchars($trajet['lieu_arivee']); ?>
                                        </h5>
                                        <small class="badge bg-warning text-dark px-2 py-1 shadow-sm">
                                            En voyage
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <div>
                                            Parti le <strong><?php echo date('d/m/Y', strtotime($trajet['date_depart'])); ?></strong> 
                                            à <strong><?php echo date('H\hi', strtotime($trajet['heure_depart'])); ?></strong>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($trajet['prix_personne']); ?></strong> crédits
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex justify-content-end">
                                        <a href="?page=profile&action=terminer-trajet&id=<?php echo $trajet['covoiturage_id']; ?>" class="btn btn-sm btn-warning fw-bold px-3 shadow-sm text-dark border-secondary-subtle">
                                             Arrivée à destination
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="liste-historique" role="tabpanel">
                    <div class="list-group list-group-flush">
                        <?php if (empty($trajetsPasses)) : ?>
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">Aucun historique de trajet disponible.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($trajetsPasses as $trajet) : ?>
                                <?php $isChauffeur = !isset($trajet['reservation_id']); ?>
                                
                                <div class="list-group-item p-4 opacity-75">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h5 class="mb-1 text-secondary fw-bold">
                                            <?php echo htmlspecialchars($trajet['lieu_depart']); ?> ➡️ <?php echo htmlspecialchars($trajet['lieu_arrivee']); ?>
                                        </h5>
                                        <small class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                            <?php echo htmlspecialchars($trajet['statut']); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <div>
                                            Le <?php echo date('d/m/Y', strtotime($trajet['date_depart'])); ?> 
                                            à <?php echo date('H\hi', strtotime($trajet['heure_depart'])); ?>
                                        </div>
                                        <div>
                                            <?php echo htmlspecialchars($trajet['prix_personne']); ?> crédits
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <?php if (!$isChauffeur && ($trajet['statut'] === 'termine' || $trajet['statut'] === 'cloture')) : ?>
                                            <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalAvis<?php echo $trajet['covoiturage_id']; ?>">
                                                Valider le trajet / Laisser un avis
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <?php include __DIR__ . '/_modal_avis.php'; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>