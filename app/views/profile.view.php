<main class="container my-5">
    <h1 class="mb-4">Mon Espace Client</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Mes Informations</div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="?page=profile">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_SESSION['nom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($_SESSION['prenom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="gsm" class="form-control" value="<?= htmlspecialchars($_SESSION['gsm'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($_SESSION['adresse'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Mes Commandes</div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <p>Aucune commande passée pour le moment.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N° Commande</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['numero_commande']) ?></td>
                                        <td><?= htmlspecialchars($order['date_commande']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($order['statut']) ?></strong>

                                            <?php if (isset($tousLesSuivis[$order['commande_id']]) && !empty($tousLesSuivis[$order['commande_id']])): ?>
                                                <br>
                                                <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#suivi-<?= $order['commande_id'] ?>">
                                                    <small>Historique complet</small>
                                                </button>

                                                <div id="suivi-<?= $order['commande_id'] ?>" class="collapse mt-2 border-start ps-2">
                                                    <small>
                                                        <?php foreach ($tousLesSuivis[$order['commande_id']] as $etape): ?>
                                                            <div class="text-muted">
                                                                <?= date('d/m/Y H:i', strtotime($etape['date_suivi'])) ?> :
                                                                <?= htmlspecialchars($etape['statut']) ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($order['statut'] === 'En attente'): ?>
                                                <a href="?page=annuler&id=<?= $order['commande_id'] ?>" class="btn btn-sm btn-danger">Annuler</a>

                                            <?php elseif ($order['statut'] === 'Terminée'): ?>

                                                <!-- Si la commande a déjà été évaluée, on affiche le badge de validation -->
                                                <?php if (in_array($order['commande_id'], $commandesAvecAvis)): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2">
                                                        <i class="bi bi-check-circle-fill"></i> Avis envoyé
                                                    </span>

                                                    <!-- Sinon, on affiche le bouton pour donner l'avis -->
                                                <?php else: ?>
                                                    <a href="?page=donner_avis&commande_id=<?= $order['commande_id'] ?>" class="btn btn-sm btn-success">Donner mon avis</a>
                                                <?php endif; ?>

                                            <?php else: ?>
                                                <span class="text-muted">Non modifiable</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>