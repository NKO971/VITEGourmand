<main>
    <div class="container my-5 py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <h2 class="fw-bold mb-4 text-center">Contactez-nous</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success shadow-sm border-0"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if (empty($success)): ?>
                <form method="post" action="?page=contact">
                    <fieldset class="fieldset-connexion p-4 bg-white">

                        <div class="mb-3">
                            <label for="titre" class="form-label small fw-medium text-secondary">Titre</label>
                            <input type="text" id="titre" name="titre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label small fw-medium text-secondary">Message</label>
                            <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label small fw-medium text-secondary">Votre email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <button type="submit" class="btn btn-vg-primary w-100 py-2.5 fw-bold">
                                Envoyer le message
                            </button>
                        </div>
                    </fieldset>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>