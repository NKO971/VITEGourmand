<main>
<!-- Section Présentation & Équipe -->
<section class="container my-5">

  <!-- Bloc 1 : L'Histoire (Texte + Image Hero) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <h1 class="fw-bold mb-3">Vite & Gourmand : 25 ans de passion, une nouvelle page à écrire</h1>
      <p class="text-muted">Depuis un quart de siècle, le cœur de Bordeaux bat au rythme des petits plats de Julie et José. Chez Vite & Gourmand, la cuisine n'est pas qu'une affaire de recettes ; c'est une histoire de fidélité, de saisons qui défilent et de ces moments précieux — des fêtes de Noël aux retrouvailles de Pâques — que l'on célèbre autour d'une table bien garnie.</p>
    </div>
    <div class="col-md-6">
      <img src="https://images.unsplash.com/photo-1760169799369-2b8574466735?auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded shadow-sm" style="height: 350px; width: 100%; object-fit: cover;" alt="Cuisinier préparant un plat en cuisine">
    </div>
  </div>

  <!-- Bloc 2 : L'Équipe -->
  <div class="row">
    <div class="col-12 bg-light p-4 rounded">
      <h2 class="h4 fw-bold mb-3">Une équipe soudée et polyvalente</h2>
      <p class="mb-4">Derrière les fourneaux et le comptoir de Vite & Gourmand, il n'y a pas de barrières, seulement une passion commune. Notre équipe est une petite brigade soudée de quatre personnes, pensée comme une mécanique de précision où la polyvalence est reine. À l'origine de cette aventure, Julie et José ne se contentent pas de diriger : ils incarnent l'esprit de l'entreprise au quotidien.</p>

      <div class="row text-center g-4">
        <div class="col-6 col-md-3">
          <img src="https://images.unsplash.com/photo-1602421312666-5883f16a7c85?auto=format&fit=crop&w=200&h=200&q=80" class="rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Portrait de José">
          <p class="fw-bold mb-0">José</p>
          <p class="text-muted small">Chef cuisinier</p>
        </div>
        <div class="col-6 col-md-3">
          <img src="https://images.unsplash.com/photo-1758519289791-ffce8889ca8c?auto=format&fit=crop&w=200&h=200&q=80" class="rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Portrait de Julie">
          <p class="fw-bold mb-0">Julie</p>
          <p class="text-muted small">Responsable service & clientèle</p>
        </div>
        <div class="col-6 col-md-3">
          <img src="https://images.unsplash.com/photo-1697020358336-3db2f502242a?auto=format&fit=crop&w=200&h=200&q=80" class="rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Portrait d'Olivier">
          <p class="fw-bold mb-0">Olivier</p>
          <p class="text-muted small">Commis de cuisine</p>
        </div>
        <div class="col-6 col-md-3">
          <img src="https://images.unsplash.com/photo-1667514045886-e79279ebe179?auto=format&fit=crop&w=200&h=200&q=80" class="rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Portrait de Nathan">
          <p class="fw-bold mb-0">Nathan</p>
          <p class="text-muted small">Livreur</p>
        </div>
      </div>
    </div>
  </div>

</section>

<!-- Avis-->
<section id="menus" class="container my-5">
  <h2 class="text-center mb-4">Retours de nos clients</h2>
    <div class="row">
        <?php if (empty($avisValides)): ?>
            <p class="text-center">Aucun avis pour le moment.</p>
        <?php else: ?>
            <?php foreach ($avisValides as $avis): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($avis['nom_client']) ?></h3>
                            <div class="text-warning mb-2">
                                <?= str_repeat('★', (int)$avis['note']) ?><?= str_repeat('☆', 5 - (int)$avis['note']) ?>
                            </div>
                            <p class="card-text">"<?= htmlspecialchars($avis['commentaire']) ?>"</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
</main>