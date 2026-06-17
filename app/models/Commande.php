<?php
class Commande
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function enregistrerCommande($data)
    {
        $sql = "INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison,  prix_menu, nombre_personne, prix_livraison, statut, utilisateur_id, menu_id) 
            VALUES (:num, :date_cmd, :date_prest, :heure, :prix_m, :nb_p, :prix_l, :statut, :u_id, :m_id)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':num'        => $data['numero_commande'],
            ':date_cmd'   => $data['date_commande'],
            ':date_prest' => $data['date_prestation'],
            ':heure'      => $data['heure_livraison'],
            ':prix_m'     => $data['prix_menu'],
            ':nb_p'       => $data['nombre_personne'],
            ':prix_l'     => $data['prix_livraison'],
            ':statut'     => $data['statut'],
            ':u_id'       => $data['user_id'],
            ':m_id'       => $data['menu_id']
        ]);
    }

    public function getOrdersByUserId($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE utilisateur_id = :user_id ORDER BY date_commande DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function canModifyOrder($orderId, $userId)
    {
        $stmt = $this->pdo->prepare("SELECT statut FROM commande WHERE id = :c_id AND utilisateur_id = :u_id");
        $stmt = $this->pdo->prepare("SELECT statut FROM commande WHERE id = :c_id AND utilisateur_id = :u_id");
        $stmt->execute(['c_id' => $orderId, 'u_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Attention : vérifie si dans ta base le champ s'appelle 'statut' ou 'status'
        return $order && $order['statut'] === 'En attente';
    }

    public function updateStatut($orderId, $userId, $nouveauStatut)
    {
        // On vérifie que la commande appartient bien à l'utilisateur
        // ET que le statut est bien 'En attente' avant de modifier
        $sql = "UPDATE commande 
            SET statut = :statut 
            WHERE commande_id = :c_id AND utilisateur_id = :u_id AND statut = 'En attente'";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':statut' => $nouveauStatut,
            ':c_id'   => $orderId,
            ':u_id'   => $userId
        ]);
    }
}
