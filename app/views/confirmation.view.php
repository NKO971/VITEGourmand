<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-success text-center p-5 shadow">
                <h2 class="mb-4">Commande validée avec succès !</h2>
                <p class="lead">Merci pour votre confiance, <?= htmlspecialchars($_SESSION['prenom']) ?>.</p>
                <p>Votre demande a été enregistrée et un mail de confirmation vous a été envoyé.</p>
                <hr>
                <a href="?page=home" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</main>