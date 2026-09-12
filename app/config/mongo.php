<?php
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $mongoUsername = getenv('MONGO_USERNAME');
    $mongoPassword = getenv('MONGO_PASSWORD');
    $mongoCluster  = getenv('MONGO_CLUSTER');
    $mongoAppName  = getenv('MONGO_APPNAME');
    $mongoDbName   = getenv('MONGO_DB_NAME');

    $uri = "mongodb+srv://{$mongoUsername}:{$mongoPassword}@{$mongoCluster}/?appName={$mongoAppName}";

    $client = new MongoDB\Client($uri);
    $db = $client->selectDatabase($mongoDbName);
} catch (Exception $e) {
    die("Erreur de connexion MongoDB : " . $e->getMessage());
}
