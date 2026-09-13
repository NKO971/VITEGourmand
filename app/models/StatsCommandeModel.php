<?php

class StatsCommandeModel
{
    /** @var \MongoDB\Collection */
    private $collection;

    public function __construct()
    {
        require_once ROOT_PATH . 'app/config/mongo.php';

        /** @var \MongoDB\Database $db */
        $this->collection = $db->selectCollection('stats_commandes');
    }

    // Upsert (update or insert) des statistiques de commande sur MongoDB
    public function upsertStats(int $commandeId, int $menuId, float $montantTotal, string $statut, string $dateCommande): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['commande_id' => $commandeId],
                ['$set' => [
                    'commande_id'   => $commandeId,
                    'menu_id'       => $menuId,
                    'montant_total' => $montantTotal,
                    'statut'        => $statut,
                    'date_commande' => $dateCommande
                ]],
                ['upsert' => true]
            );
            return $result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur upsertStats : " . $e->getMessage());
            return false;
        }
    }

    public function getNombreCommandesParMenu(): array
    {
        try {
            $pipeline = [
                ['$match' => ['statut' => ['$ne' => 'Annulée']]],
                ['$group' => [
                    '_id'     => '$menu_id',
                    'nombre'  => ['$sum' => 1]
                ]],
                ['$sort' => ['_id' => 1]]
            ];

            $cursor = $this->collection->aggregate($pipeline);
            $result = [];
            foreach ($cursor as $doc) {
                $result[] = [
                    'menu_id' => (int)$doc['_id'],
                    'nombre'  => (int)$doc['nombre']
                ];
            }
            return $result;
        } catch (Exception $e) {
            error_log("Erreur getNombreCommandesParMenu : " . $e->getMessage());
            return [];
        }
    }

    public function getChiffreAffaires(?int $menuId = null, ?string $dateDebut = null, ?string $dateFin = null): float
    {
        try {
            $match = ['statut' => ['$ne' => 'Annulée']];

            if ($menuId !== null) {
                $match['menu_id'] = $menuId;
            }

            if ($dateDebut !== null || $dateFin !== null) {
                $match['date_commande'] = [];
                if ($dateDebut !== null) {
                    $match['date_commande']['$gte'] = $dateDebut;
                }
                if ($dateFin !== null) {
                    $match['date_commande']['$lte'] = $dateFin;
                }
            }

            $pipeline = [
                ['$match' => $match],
                ['$group' => [
                    '_id'   => null,
                    'total' => ['$sum' => '$montant_total']
                ]]
            ];

            $cursor = $this->collection->aggregate($pipeline);
            $result = iterator_to_array($cursor);

            return !empty($result) ? (float)$result[0]['total'] : 0.0;
        } catch (Exception $e) {
            error_log("Erreur getChiffreAffaires : " . $e->getMessage());
            return 0.0;
        }
    }

    public function getNombreAnnulationsMoisEnCours(): int
    {
        try {
            $debutMois = date('Y-m-01');
            $finMois   = date('Y-m-t'); // 't' = dernier jour du mois courant

            $pipeline = [
                ['$match' => [
                    'statut'        => 'Annulée',
                    'date_commande' => ['$gte' => $debutMois, '$lte' => $finMois]
                ]],
                ['$count' => 'total']
            ];

            $cursor = $this->collection->aggregate($pipeline);
            $result = iterator_to_array($cursor);

            return !empty($result) ? (int)$result[0]['total'] : 0;
        } catch (Exception $e) {
            error_log("Erreur getNombreAnnulationsMoisEnCours : " . $e->getMessage());
            return 0;
        }
    }
}