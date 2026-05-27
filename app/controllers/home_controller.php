<?php 

function homeController() { 
     require_once(__DIR__ . '/baseController.php');
    BaseController::render( 
        "Accueil",
        "home.view.php",
        ["custom.css"],  

    );       

}