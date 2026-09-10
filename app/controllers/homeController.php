<?php 
require_once ROOT_PATH . 'app/models/AvisModel.php';

function homeController() 
{ 
    // Récupération des avis validés
    $avisModel = new AvisModel();
    $avisValides = $avisModel->getAvisByStatut('valide');

    // Chargement de la vue via le layout Front-Office
    BaseController::render( 
        "Accueil",
        "home.view.php",
        ["css/custom.css"],
        [],                 
        [                   
            'avisValides' => $avisValides
        ],
        'front'            
    );       
}
