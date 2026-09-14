<?php 
require_once ROOT_PATH . 'app/models/AvisModel.php';

function homeController($pdo) 
{ 
    // Récupération des avis validés
    $avisModel = new AvisModel();
    $avisValides = $avisModel->getAvisByStatut('valide');

    // Horaires dynamiques pour le footer public
    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    // Chargement de la vue via le layout Front-Office
    BaseController::render( 
        "Accueil",
        "home.view.php",
        ["css/custom.css"],
        [],                 
        [                   
            'avisValides'   => $avisValides,
            'horairesFooter' => $horairesFooter
        ],
        'front'            
    );       
}
