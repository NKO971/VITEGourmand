<?php
class Plat {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createPlat(string $titrePlat, int $actif) {
        $stmt = $this->pdo->prepare('INSERT INTO plat (titre_plat, actif) VALUES (:titre, :actif)');
        $stmt->execute([
            ':titre' => $titrePlat,
            ':actif' => $actif
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updatePlat($platId, string $titrePlat, $photoData = null) {
        if ($photoData !== null) {
            $sql = "UPDATE plat SET titre_plat = :titre, photo = :photo WHERE plat_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':titre', $titrePlat);
            $stmt->bindValue(':photo', $photoData, PDO::PARAM_LOB);
            $stmt->bindValue(':id', $platId, PDO::PARAM_INT);
            return $stmt->execute();
        }

        $sql = "UPDATE plat SET titre_plat = :titre WHERE plat_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => $titrePlat,
            ':id'    => $platId
        ]);
    }

    public function toggleStatus($platId, $actif) {
        $stmt = $this->pdo->prepare("UPDATE plat SET actif = :actif WHERE plat_id = :id");
        $stmt->execute([
            ':actif' => $actif,
            ':id'    => $platId
        ]);
        return $stmt->rowCount();
    }
}