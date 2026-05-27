<body>
    <main class="container py-5">
        <div class="profile-employe d-flex align-items-center mb-5 p-3 bg-white shadow-sm rounded-4 ">
            <div class="avatar-wrapper">
                <img src="/EcoRide/Photo profile/pexels-italo-melo-881954-2379005.jpg" alt="Photo de l'employé" class="avatar-img">
                <span class="status-indicator"></span>
            </div>

            <div class="ms-3">
                <h3 class="h5 mb-0" style="color: var(--color-dark);" id="nom-employe">Bienvenue, <?php echo htmlspecialchars($employee['pseudo'] ?? 'Employé'); ?></h3>
                <p class="small mb-0" style="color: var(--color-light);">Équipe de modération • EcoRide</p>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h1 class="mb-5">Tableau de bord de modération</h1>

        <section id="section-avis" class="card shadow-sm mb-5">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0">Avis en attente de validation</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Covoiturage</th>
                                <th>Auteur</th>
                                <th>Chauffeur visé</th>
                                <th>Note</th>
                                <th>Commentaire</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($avisEnAttente)): ?>
                                <?php foreach ($avisEnAttente as $avis): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($avis['covoiturage_id'] ?? ''); ?></td>
                                    <td><strong><?php echo htmlspecialchars($avis['passager_pseudo'] ?? 'Anonyme'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($avis['chauffeur_pseudo'] ?? 'Chauffeur'); ?></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($avis['note'] ?? '0'); ?> / 5</span></td>
                                    <td>
                                        <span class="text-dark d-block fw-semibold mb-1"><?php echo htmlspecialchars($avis['commentaire'] ?? ''); ?></span>
                                        <span class="badge bg-light text-secondary border small">
                                            Déroulement : <?php echo htmlspecialchars($avis['deroulement'] ?? 'Non spécifié'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?page=employe&action=valider_avis&id=<?php echo $avis['avis_id']; ?>" class="btn btn-sm btn-success text-white me-1">Valider</a>
                                        <a href="?page=employe&action=refuser_avis&id=<?php echo $avis['avis_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Refuser cet avis définitivement ?');">Refuser</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun avis en attente</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="section-signalements" class="card shadow-sm">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0">Trajets signalés (Incidents)</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>N° Covoiturage</th>
                                <th>Descriptif du Trajet</th>
                                <th>Conducteur Intéressé</th>
                                <th>Passager Intéressé</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($trajetsIncidents)): ?>
                                <?php foreach ($trajetsIncidents as $trajet): ?>
                                <tr>
                                    <td><span class="fw-bold">#<?php echo $trajet['covoiturage_id']; ?></span></td>
                                    <td>
                                        <div class="small">
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($trajet['lieu_depart']); ?> → <?php echo htmlspecialchars($trajet['lieu_arivee']); ?></span><br>
                                            <span class="text-muted">Départ : <?php echo $trajet['date_depart']; ?> à <?php echo $trajet['heure_depart']; ?></span><br>
                                            <span class="text-muted">Arrivée : <?php echo $trajet['date_arrivee'] ?? '--'; ?> à <?php echo $trajet['heure_arrivee'] ?? '--'; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <strong><?php echo htmlspecialchars($trajet['chauf_pseudo']); ?></strong><br>
                                            <span class="text-muted"><?php echo htmlspecialchars($trajet['chauf_email']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <strong><?php echo htmlspecialchars($trajet['pass_pseudo']); ?></strong><br>
                                            <span class="text-muted"><?php echo htmlspecialchars($trajet['pass_email']); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucun signalement</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
