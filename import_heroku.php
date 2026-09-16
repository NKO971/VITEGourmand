<?php
$host = 'e7qyahb3d90mletd.chr7pe7iynqr.eu-west-1.rds.amazonaws.com';
$port = 3306;
$db   = 'hwfyux32gqf7x6v4';
$user = 'cgkhiwrwpwaflg66';
$pass = 'pt87129pmxvcj8zk';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
    echo "Connexion OK\n";

    foreach (['database/schema.sql', 'database/fixtures.sql'] as $file) {
        echo "Import de $file...\n";
        $pdo->exec(file_get_contents($file));
        echo "$file importé avec succès.\n";
    }
} catch (PDOException $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}