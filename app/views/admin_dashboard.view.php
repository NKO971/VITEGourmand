<div class="container-fluid my-4 admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark fw-bold mb-0">Dashboard Administrateur</h1>
    </div>

    <div class="row g-4">
        <!-- CARD 1 : Graphique nombre de commandes par menu -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title text-primary mb-0"><i class="bi bi-bar-chart me-2" aria-hidden="true"></i>Nombre de commandes par menu</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($commandesParMenu)): ?>
                        <p class="text-muted text-center py-4">Aucune donnée disponible pour le moment.</p>
                    <?php else: ?>
                        <canvas id="chartCommandesParMenu" height="280"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CARD 2 : Annulations du mois en cours -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title text-danger mb-0"><i class="bi bi-x-circle me-2" aria-hidden="true"></i>Annulations ce mois-ci</h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <span class="display-3 fw-bold text-danger"><?= (int)$annulationsMoisEnCours ?></span>
                    <?php
                    $moisFrancais = [
                        1 => 'Janvier',
                        2 => 'Février',
                        3 => 'Mars',
                        4 => 'Avril',
                        5 => 'Mai',
                        6 => 'Juin',
                        7 => 'Juillet',
                        8 => 'Août',
                        9 => 'Septembre',
                        10 => 'Octobre',
                        11 => 'Novembre',
                        12 => 'Décembre'
                    ];
                    $moisActuel = $moisFrancais[(int)date('n')] . ' ' . date('Y');
                    ?>
                    <p class="text-muted mt-2 mb-0">commande(s) annulée(s) en <?= htmlspecialchars($moisActuel) ?></p>
                </div>
            </div>
        </div>

        <!-- CARD 3 : Chiffre d'affaires avec filtres -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title text-success mb-0"><i class="bi bi-currency-euro me-2" aria-hidden="true"></i>Chiffre d'affaires</h5>
                </div>
                <div class="card-body">
                    <form id="formFiltresCA" class="row g-3 align-items-end mb-4" onsubmit="event.preventDefault();">
                        <div class="col-md-4">
                            <label for="filtre_ca_menu" class="form-label small fw-semibold">Menu</label>
                            <select class="form-select" id="filtre_ca_menu">
                                <option value="">Tous les menus</option>
                                <?php foreach ($tousLesMenus as $menu): ?>
                                    <option value="<?= $menu['menu_id'] ?>"><?= htmlspecialchars($menu['titre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtre_ca_date_debut" class="form-label small fw-semibold">Du</label>
                            <input type="date" class="form-control" id="filtre_ca_date_debut">
                        </div>
                        <div class="col-md-3">
                            <label for="filtre_ca_date_fin" class="form-label small fw-semibold">Au</label>
                            <input type="date" class="form-control" id="filtre_ca_date_fin">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btn-appliquer-filtres-ca" class="btn btn-primary w-100">Filtrer</button>
                        </div>
                    </form>

                    <div class="text-center py-3">
                        <span class="text-muted d-block mb-1">Chiffre d'affaires total</span>
                        <span class="display-4 fw-bold text-success" id="ca-total">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Injection des données PHP vers le JS pour le graphique
    const commandesParMenuData = <?= json_encode($commandesParMenu, JSON_UNESCAPED_UNICODE) ?>;
</script>