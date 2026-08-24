<?php 
require_once ROOT_PATH . 'app/models/AvisModel.php';

$avisModel = new AvisModel();
$avisValides = $avisModel->getAvisByStatut('valide');

function homeController() { 
     require_once(__DIR__ . '/baseController.php');
    BaseController::render( 
        "Accueil",
        "home.view.php",
        ["custom.css"],  

    );       

}
