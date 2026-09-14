<?php

function mentionsLegalesController($pdo)
{
    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    BaseController::render(
        "Mentions légales - VITEGourmand",
        "mentions_legales.view.php",
        [],
        [],
        [
            'horairesFooter' => $horairesFooter
        ]
    );
}

function cgvController($pdo)
{
    require_once ROOT_PATH . 'app/models/Horaire.php';
    $horaireModel = new Horaire($pdo);
    try {
        $horairesFooter = $horaireModel->getAll();
    } catch (PDOException $e) {
        error_log("Erreur chargement horaires footer : " . $e->getMessage());
        $horairesFooter = [];
    }

    BaseController::render(
        "Conditions Générales de Vente - VITEGourmand",
        "cgv.view.php",
        [],
        [],
        [
            'horairesFooter' => $horairesFooter
        ]
    );
}