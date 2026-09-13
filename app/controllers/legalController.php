<?php

function mentionsLegalesController()
{
    BaseController::render(
        "Mentions légales - VITEGourmand",
        "mentions_legales.view.php",
        [],
        [],
        []
    );
}

function cgvController()
{
    BaseController::render(
        "Conditions Générales de Vente - VITEGourmand",
        "cgv.view.php",
        [],
        [],
        []
    );
}