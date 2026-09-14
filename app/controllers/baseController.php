<?php
class BaseController
{
    /**
     * @param string $title Titre de la page
     * @param string $viewFile Nom du fichier vue
     * @param array $additionalCss CSS spécifiques
     * @param array $additionalJs JS spécifiques
     * @param array $data Données pour la vue
     * @param string|bool $layout 'front' (par défaut), 'back' ou false
     */
    public static function render(
        $title, 
        $viewFile, 
        $additionalCss = [], 
        $additionalJs = [], 
        $data = [], 
        $layout = 'front'
    ) {
        if (!empty($data)) {
            extract($data);
        }

        $specificCss = array_merge(["css/bootstrap.min.css", "css/base.css"], $additionalCss);
        $specificJs  = array_merge(["js/jquery-3.7.1.min.js", "js/bootstrap.bundle.min.js"], $additionalJs);

        if ($layout === 'front') {
            require_once __DIR__ . '/../views/includes/header.php';
        } elseif ($layout === 'back') {
            require_once __DIR__ . '/../views/includes/header_back.php';
        }

        require_once __DIR__ . '/../views/' . $viewFile;

        if ($layout === 'front') {
            require_once __DIR__ . '/../views/includes/footer.php';
        } elseif ($layout === 'back') {
            require_once __DIR__ . '/../views/includes/footer_back.php';
        }
    }
}
