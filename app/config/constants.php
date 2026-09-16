<?php

// Connexion à la base de données : Heroku (via JAWSDB_URL) sinon config locale XAMPP
$jawsdbUrl = getenv('JAWSDB_URL');

if ($jawsdbUrl) {
    $dbParts = parse_url($jawsdbUrl);

    define('DB_HOST', $dbParts['host']);
    define('DB_NAME', ltrim($dbParts['path'], '/'));
    define('DB_USER', $dbParts['user']);
    define('DB_PASS', $dbParts['pass']);

    define('BASE_URL', '');
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'vitegourmand');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    define('BASE_URL', '/VITEGourmand/public');
}

// Dictionnaire des Tables pour éviter les fautes de frappe comme dans EcoRide
define('TABLE_MENU', 'menu');
define('TABLE_PLAT', 'plat');
define('TABLE_THEME', 'theme');
define('TABLE_REGIME', 'regime');
define('TABLE_USER', 'utilisateur');

// Clés de la partie NoSQL / JSON
// define('JSON_KEY_ENTREES', 'entrees');
// define('JSON_KEY_PLATS', 'plats');
// define('JSON_KEY_DESSERTS', 'desserts');
// define('JSON_KEY_ALLERGENES', 'allergenes');