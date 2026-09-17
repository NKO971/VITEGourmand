<?php
class Plat
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createPlat(string $titrePlat, int $actif)
    {
        $stmt = $this->pdo->prepare('INSERT INTO plat (titre_plat, actif) VALUES (:titre, :actif)');
        $stmt->execute([
            ':titre' => $titrePlat,
            ':actif' => $actif
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updatePlat($platId, string $titrePlat, $photoData = null)
    {
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

    public function toggleStatus($platId, $actif)
    {
        $stmt = $this->pdo->prepare("UPDATE plat SET actif = :actif WHERE plat_id = :id");
        $stmt->execute([
            ':actif' => $actif,
            ':id'    => $platId
        ]);
        return $stmt->rowCount();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM plat ORDER BY plat_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────────
    // Allergenes par plat (table plat_allergene)
    // ─────────────────────────────────────────────

    /**
     * Retourne les allergenes d'un plat (id + libelle).
     * @return array<int, array{allergene_id:int, libelle:string}>
     */
    public function getAllergenesByPlatId($platId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.allergene_id, a.libelle
            FROM plat_allergene pa
            JOIN allergene a ON a.allergene_id = pa.allergene_id
            WHERE pa.plat_id = ?
            ORDER BY a.libelle ASC
        ");
        $stmt->execute([(int)$platId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les allergenes de plusieurs plats en une seule requete.
     * @param int[] $platIds
     * @return array<int, array<int, array{allergene_id:int, libelle:string}>> [plat_id => [...]]
     */
    public function getAllergenesForPlats(array $platIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $platIds)));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("
            SELECT pa.plat_id, a.allergene_id, a.libelle
            FROM plat_allergene pa
            JOIN allergene a ON a.allergene_id = pa.allergene_id
            WHERE pa.plat_id IN ($placeholders)
            ORDER BY pa.plat_id ASC, a.libelle ASC
        ");
        $stmt->execute($ids);

        $result = [];
        foreach ($ids as $id) {
            $result[$id] = [];
        }
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['plat_id']][] = [
                'allergene_id' => (int)$row['allergene_id'],
                'libelle'      => $row['libelle'],
            ];
        }
        return $result;
    }

    /**
     * Remplace la liste d'allergenes d'un plat par celle fournie
     * (supprime puis reinsere, dans une transaction).
     * @param int[] $allergeneIds
     */
    public function saveAllergenes(int $platId, array $allergeneIds): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $allergeneIds))));

        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare("DELETE FROM plat_allergene WHERE plat_id = ?");
            $del->execute([$platId]);

            if (!empty($ids)) {
                $insert = $this->pdo->prepare("INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (?, ?)");
                foreach ($ids as $allergeneId) {
                    $insert->execute([$platId, $allergeneId]);
                }
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
