<footer class="vg-footer text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <div class="col-12 col-md-4">
                <h5 class="fw-bold font-poppins mb-3 text-orange">VITE<span class="text-white">Gourmand</span></h5>
                <p class="text-muted small">Des plats d'exception cuisinés avec amour par vos chefs locaux, livrés à toute vitesse.</p>
                <div class="vg-social-links d-flex gap-3 mt-3">
                    <a href="#" class="text-white opacity-75 opacity-100-hover"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white opacity-75 opacity-100-hover"><i class="bi bi-facebook fs-5"></i></a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <h6 class="text-uppercase fw-bold font-poppins letter-spacing-1 mb-3 text-white-50 small">Horaires d'ouverture</h6>
                <ul class="list-unstyled text-muted small lh-lg">
                    <li class="d-flex justify-content-between"><span>Mardi - Samedi :</span> <span class="text-white">10h00 - 22h00</span></li>
                    <li class="d-flex justify-content-between"><span>Dimanche :</span> <span class="text-white">10h00 - 14h00</span></li>
                    <li class="d-flex justify-content-between text-danger-soft"><span>Lundi :</span> <span class="badge bg-danger-soft">Fermé</span></li>
                </ul>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <h6 class="text-uppercase fw-bold font-poppins letter-spacing-1 mb-3 text-white-50 small">Informations</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="#" class="vg-footer-link text-muted text-decoration-none">Mentions légales</a></li>
                    <li><a href="#" class="vg-footer-link text-muted text-decoration-none">Conditions Générales de Vente</a></li>
                    <li><a href="/VITEGourmand/public/?page=contact" class="vg-footer-link text-muted text-decoration-none">Nous contacter</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-4 opacity-10">

        <div class="row">
            <div class="col text-center text-muted small">
                <p class="mb-0">&copy; <?= date('Y'); ?> VITEGourmand. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>
<?php if (isset($specificJs)): ?>
        <?php foreach ($specificJs as $js): ?>
            <script src="<?= $js; ?>"></script>
        <?php endforeach; ?>
<?php endif; ?>
</body>
</html>