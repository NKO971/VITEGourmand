<!-- Section Présentation & Équipe -->
<section class="container my-5">

  <!-- Bloc 1 : L'Histoire (Texte + Image) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <h2 class="fw-bold mb-3">Vite & Gourmand : 25 ans de passion, une nouvelle page à écrire</h2>
      <p class="text-muted">Depuis un quart de siècle, le cœur de Bordeaux bat au rythme des petits plats de Julie et José. Chez Vite & Gourmand, la cuisine n'est pas qu'une affaire de recettes ; c’est une histoire de fidélité, de saisons qui défilent et de ces moments précieux — des fêtes de Noël aux retrouvailles de Pâques — que l'on célèbre autour d'une table bien garnie.</p>
    </div>
<div class="col-12 col-md-8 mx-auto">
      
      <div id="carouselExample" class="carousel slide shadow-sm rounded overflow-hidden">
        
        <div class="carousel-inner">
          
          <div class="carousel-item active">
            <img src="Image/gallery/gallery01.jpg" class="d-block w-100" style="height: 350px; object-fit: cover;" alt="Plat Vite & Gourmand 1">
          </div>
          
          <div class="carousel-item">
            <img src="Image/gallery/gallery02.jpg" class="d-block w-100" style="height: 350px; object-fit: cover;" alt="Plat Vite & Gourmand 2">
          </div>
          
          <div class="carousel-item">
            <img src="Image/gallery/gallery03.jpg" class="d-block w-100" style="height: 350px; object-fit: cover;" alt="Plat Vite & Gourmand 3">
          </div>
          
          <div class="carousel-item">
            <img src="Image/gallery/gallery04.jpg" class="d-block w-100" style="height: 350px; object-fit: cover;" alt="Plat Vite & Gourmand 4">
          </div> 

        </div>

        <!-- Boutons de contrôle -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Suivant</span>
        </button>

      </div>

    </div>
  </div>

  <!-- Bloc 2 : L'Équipe (Pleine largeur en dessous) -->
  <div class="row">
    <div class="col-12 bg-light p-4 rounded">
      <h3 class="h4 fw-bold mb-3">Une équipe soudée et polyvalente</h3>
      <p class="mb-0">Derrière les fourneaux et le comptoir de Vite & Gourmand, il n’y a pas de barrières, seulement une passion commune. Notre équipe est une petite brigade soudée de quatre personnes, pensée comme une mécanique de précision où la polyvalence est reine. À l'origine de cette aventure, Julie et José ne se contentent pas de diriger : ils incarnent l'esprit de l'entreprise au quotidien. Notre force réside dans notre complémentarité, articulée autour de deux binômes : deux cuisiniers passionnés et deux serveurs dévoués.</p>
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
                            <h5 class="card-title"><?= htmlspecialchars($avis['nom_client']) ?></h5>
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