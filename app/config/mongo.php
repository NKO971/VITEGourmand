<?php
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $uri = "mongodb+srv://luidginicolas_db_user:ufV3RPQVSGe1HbBH@clustervitegourmand.2nvuuct.mongodb.net/?appName=ClusterViteGourmand";
    $client = new MongoDB\Client($uri);
    $db = $client->selectDataBase('ViteGourmand');
} catch (Exception $e) {
    die("Erreur de connexion MongoDB : " . $e->getMessage());
}
