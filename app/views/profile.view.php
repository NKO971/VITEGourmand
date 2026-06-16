<main class="container my-5">
    <h1 class="mb-4">Mon Espace Client</h1>

    <div class="row">
        <div class="col-md-4">
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

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Mes Commandes</div>
                <div class="card-body">
                    <p class="text-muted">Aucune commande en cours pour le moment.</p>
                </div>
            </div>
        </div>
    </div>
</main>