<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/models/AvisModel.php';

$avisModel = new AvisModel();

echo "--- Insertion des avis de test ---\n";

$a1 = $avisModel->createAvis("Thomas Dupuis", 5, "Un régal ! Le menu gastronomique était parfait.", 101);
$a2 = $avisModel->createAvis("Sophie Martin", 4, "Très bon repas, livraison dans les temps.", 102);
$a3 = $avisModel->createAvis("Lucas Bernard", 2, "Le plat est arrivé tiède.", 103);

if ($a1 && $a2 && $a3) {
    echo "3 avis insérés avec succès (statut 'en_attente') !\n\n";
} else {
    echo "Erreur lors de l'insertion.\n\n";
}

echo "--- Lecture des avis en attente ---\n";
$avisEnAttente = $avisModel->getAvisByStatut('en_attente');

foreach ($avisEnAttente as $avis) {
    echo "ID: " . $avis['_id'] . " | Client: " . $avis['nom_client'] . " | Note: " . $avis['note'] . "/5\n";
    echo "Commentaire: " . $avis['commentaire'] . "\n";
    echo "--------------------------------------------------\n";
}