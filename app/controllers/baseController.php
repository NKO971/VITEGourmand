<?php
class BaseController
{

    // On ajoute un 5ème paramètre : $data = []
    public static function render($title, $viewFile, $additionalCss = [], $additionalJs = [], $data = [])
    {

        // Si le contrôleur nous a envoyé des données, on les transforme en vraies variables
        if (!empty($data)) {
            extract($data);
        }

        // CSS par défaut
        $specificCss = array_merge([
            "css/bootstrap.min.css",
            "css/base.css"
        ], $additionalCss);

        // JS par défaut
        $specificJs = array_merge([
            "js/jquery-3.7.1.min.js",
            "js/bootstrap.bundle.min.js"
        ], $additionalJs);

        // Inclusions dans le bon ordre
        require_once(__DIR__ . '/../views/includes/header.php');
        require_once(__DIR__ . '/../views/' . $viewFile);
        require_once(__DIR__ . '/../views/includes/footer.php');
    }
}
