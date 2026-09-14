<?php

class ZoneLivraison
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDistanceByCodePostal(string $codePostal): ?float
    {
        $stmt = $this->pdo->prepare("SELECT distance_km FROM zone_livraison WHERE code_postal = :cp");
        $stmt->execute(['cp' => $codePostal]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);

        // null = zone introuvable, différent de 0 km (certaines zones sont légitimement à 0 km, ex: centre-ville)
        return $zone ? (float)$zone['distance_km'] : null;
    }
}