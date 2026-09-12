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
}