<?php

function menusController() { 
     require_once(__DIR__ . '/baseController.php');
    BaseController::render( 
        "Nos Menus",
        "menus.view.php",
        ["custom.css"],  

    );       

}