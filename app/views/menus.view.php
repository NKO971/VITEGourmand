<!-- Filtres des menus -->
<section id="menus" class="container my-5">
  <div class="bg-light p-3 rounded shadow-sm border border-secondary-subtle mb-5">
    <h3 class="h6 mb-3 text-secondary fw-bold text-uppercase">Filtres de recherche</h3>

    <form class="row g-2 align-items-center">
      
      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small">Prix Min (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-min" placeholder="0">
          </div>
          <div class="col-6">
            <label class="form-label small">Prix Max (€)</label>
            <input type="number" class="form-control form-control-sm" id="prix-max" placeholder="100">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small">Convives</label>
            <select class="form-select form-select-sm"><option>Tous</option></select>
          </div>
          <div class="col-6">
            <label class="form-label small">Régime</label>
            <select class="form-select form-select-sm"><option>Tous</option></select>
          </div>
        </div>
      </div>

      <div class="col-12 mt-2">
        <div class="row g-2 align-items-end">
          <div class="col-md-9">
            <label class="form-label small">Thème du menu</label>
            <select class="form-select form-select-sm"><option>Soleil et apéros</option></select>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary btn-sm w-100">Réinitialiser</button>
          </div>
        </div>
      </div>

    </form>
  </div>

  <div id="vignettes-container" class="row g-4"></div>
</section>
<!-- Fin des filtres -->

<!-- Sections des menus -->
<section id="menu-categories" class="container my-5 col-2">
  <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
  <div class="position-relative">
    <img src="Image/photo_accueil.jpg" class="card-img-top" alt="Menu" style="height: 200px; object-fit: cover;">
    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Stock : 5</span>
  </div>
  
  <div class="card-body">
    <h5 class="card-title fw-bold">Nom du Menu</h5>
    <p class="text-muted small mb-2"><i class="bi bi-tag"></i> Thème : Noël</p>
    
    <div class="d-flex gap-2 mb-3">
      <span class="badge bg-success-subtle text-success">Végétarien</span>
      <span class="badge bg-info-subtle text-info">Min. 4 pers</span>
    </div>

    <p class="card-text small text-truncate">Courte description du menu ici...</p>
    
    <div class="d-flex justify-content-between align-items-center mt-3">
      <span class="h5 mb-0 text-primary fw-bold">45€</span>
      <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">Voir détails</button>
    </div>
  </div>
</div>
</section>