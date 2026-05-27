<?php
// On charge d'abord le dictionnaire de constantes
require_once __DIR__ . '/constants.php';

try {
    // On tente la connexion en utilisant les constantes globales
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", 
        DB_USER, 
        DB_PASS
    );
    
    // Configuration pour lever des exceptions en cas d'erreur SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configuration pour récupérer les données sous forme de tableau associatif par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la connexion échoue, le script s'arrête proprement et affiche l'erreur
    die("Échec de la connexion à la base de données : " . $e->getMessage());
}