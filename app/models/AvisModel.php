<?php

use MongoDB\BSON\ObjectId;

class AvisModel
{
    /** @var \MongoDB\Collection */
    private $collection;

    public function __construct()
    {
        // Inclusion du fichier de configuration MongoDB
        require_once ROOT_PATH . 'app/config/mongo.php';
        
        /** @var \MongoDB\Database $db */
        $this->collection = $db->selectCollection('avis');
    }

    // Récupérer tous les avis selon le statut
    public function getAvisByStatut(string $statut): array 
    {
        $cursor = $this->collection->find(
            ['statut' => $statut],
            ['sort' => ['date_creation' => -1]]
        );
        /** @var \Traversable $cursor */
        return iterator_to_array($cursor);
    }

    // Valider ou refuser un avis à partir de son ObjectId
    public function updateStatut(string $id, string $statut): bool 
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => ['statut' => $statut]]
            );
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du statut de l'avis : " . $e->getMessage());
            return false;
        }
    }

    // Ajouter un nouvel avis
    public function createAvis(string $nomClient, int $note, string $commentaire, int $commandeId): bool 
    {
        $result = $this->collection->insertOne([
            'commande_id'   => $commandeId,
            'nom_client'    => $nomClient,
            'note'          => $note,
            'commentaire'   => $commentaire,
            'statut'        => 'en_attente',
            'date_creation' => date('Y-m-d H:i:s')
        ]);
        return $result->getInsertedCount() > 0;
    }
}