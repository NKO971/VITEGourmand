<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <!-- Carte principale avec une ombre douce -->
            <div class="card shadow border-0 rounded-3">
                
                <!-- En-tête de la carte aux couleurs de ton app -->
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="card-title mb-1">Votre avis nous intéresse !</h3>
                    <p class="mb-0 text-white-50">Commande n°<?= htmlspecialchars($_GET['commande_id'] ?? '') ?></p>
                </div>
                
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Merci d'avoir choisi VITEGourmand. Prenez un court instant pour évaluer votre expérience.
                    </p>

                    <form action="?page=traitement_avis" method="POST">
                        <!-- ID de la commande masqué pour la transmission -->
                        <input type="hidden" name="commande_id" value="<?= htmlspecialchars($_GET['commande_id'] ?? '') ?>">

                        <!-- Section Note avec un Select "Stars" beaucoup plus pro -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quelle note attribuez-vous ?</label>
                            <select name="note" class="form-select form-select-lg border-2" required>
                                <option value="" disabled selected>Choisir une note...</option>
                                <option value="5">⭐⭐⭐⭐⭐ - Excellent !</option>
                                <option value="4">⭐⭐⭐⭐ - Très bon</option>
                                <option value="3">⭐⭐⭐ - Correct</option>
                                <option value="2">⭐⭐ - Moyen</option>
                                <option value="1">⭐ - Décevant</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Votre commentaire :</label>
                            <textarea name="commentaire" class="form-control border-2" rows="4" 
                                      placeholder="Qu'avez-vous pensé de la qualité du repas, du temps de livraison... ?" required></textarea>
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <a href="?page=profile" class="btn btn-outline-secondary w-50">Annuler</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold">Envoyer mon avis</button>
                        </div>
                    </form>
                </div>
                
            </div>
            
        </div>
    </div>
</main>