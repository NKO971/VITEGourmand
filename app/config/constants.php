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

} elseif (getenv('DB_HOST')) {
    // Docker : configuration fournie par les variables d'environnement (compose.yaml + .env)
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASS'));

    define('BASE_URL', '');
    
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'vitegourmand');

    $localConfigPath = __DIR__ . '/constants.local.php';
    if (file_exists($localConfigPath)) {
        require $localConfigPath;
    } else {
        die("Config locale manquante : copie constants.local.php.example vers constants.local.php et renseigne tes identifiants.");
    }

    define('BASE_URL', '/VITEGourmand/public');
}

// Dictionnaire des Tables pour éviter les erreurs de frappe dans le code 
define('TABLE_MENU', 'menu');
define('TABLE_PLAT', 'plat');
define('TABLE_THEME', 'theme');
define('TABLE_REGIME', 'regime');
define('TABLE_USER', 'utilisateur');
