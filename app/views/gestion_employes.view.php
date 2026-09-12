<div class="container-fluid my-4 gestion-employes">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark fw-bold mb-0">Gestion des Employés</h1>
        <button class="btn btn-sm btn-outline-primary" id="btn-add-employe"><i class="bi bi-plus-circle me-1"></i> Ajouter un employé</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title text-primary mb-0"><i class="bi bi-people me-2"></i>Comptes employés</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employes)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Aucun employé enregistré.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employes as $employe): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars((string)$employe['utilisateur_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($employe['email']) ?></td>
                                    <td>
                                        <span class="badge badge-status <?= ($employe['is_active'] ?? 0) == 1 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ($employe['is_active'] ?? 0) == 1 ? 'Actif' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm <?= ($employe['is_active'] ?? 0) == 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> btn-toggle-employe"
                                            data-id="<?= $employe['utilisateur_id'] ?>"
                                            data-active="<?= ($employe['is_active'] ?? 0) == 1 ? 0 : 1 ?>">
                                            <i class="bi <?= ($employe['is_active'] ?? 0) == 1 ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
                                            <?= ($employe['is_active'] ?? 0) == 1 ? 'Désactiver' : 'Activer' ?>
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

    <?php require_once ROOT_PATH . 'app/views/partials/_modal_create_employe.php'; ?>
</div>