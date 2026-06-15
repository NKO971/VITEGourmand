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
            ':u_id'       => $data['utilisateur_id'],
            ':m_id'       => $data['menu_id']
        ]);
    }
}
