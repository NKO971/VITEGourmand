<div class="container-fluid my-4 gestion-horaires">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark fw-bold mb-0">Gestion des Horaires</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title text-primary mb-0"><i class="bi bi-clock me-2"></i>Horaires d'ouverture</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jour</th>
                            <th>Heure d'ouverture</th>
                            <th>Heure de fermeture</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($horaires)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Aucun horaire enregistré.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($horaires as $horaire): ?>
                                <tr data-jour="<?= htmlspecialchars($horaire['jour']) ?>">
                                    <td><strong><?= htmlspecialchars($horaire['jour']) ?></strong></td>
                                    <td>
                                        <input type="text" class="form-control input-ouverture"
                                               value="<?= htmlspecialchars($horaire['heure_ouverture']) ?>"
                                               placeholder="ex: 10:00 ou Fermé">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-fermeture"
                                               value="<?= htmlspecialchars($horaire['heure_fermeture']) ?>"
                                               placeholder="ex: 22:00 ou Fermé">
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary btn-save-horaire"
                                                data-jour="<?= htmlspecialchars($horaire['jour']) ?>">
                                            <i class="bi bi-check-lg"></i> Enregistrer
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
</div>