<?php

class Horaire {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les 7 jours de la semaine avec leurs horaires.
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM horaire ORDER BY horaire_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour les horaires d'un jour donné.
     */
    public function updateHoraire(string $jour, string $ouverture, string $fermeture): bool {
        $sql = "UPDATE horaire 
                SET heure_ouverture = :ouverture, heure_fermeture = :fermeture 
                WHERE jour = :jour";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':ouverture' => $ouverture,
            ':fermeture' => $fermeture,
            ':jour'      => $jour
        ]);
    }
}