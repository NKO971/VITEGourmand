<?php
class Menu
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getMenuById($menuId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
        $stmt->execute([$menuId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createMenu(array $data)
    {
        $sql = "INSERT INTO menu (titre, description, prix_par_personne, quantite_restante, nombre_personne_minimum, theme_id, regime_id, composition, conditions_stockage, actif"
            . (!empty($data['image']) ? ", image" : "") . ")
            VALUES (:titre, :description, :prix, :stock, :min_pers, :theme_id, :regime_id, :composition, :conditions_stockage, 1"
            . (!empty($data['image']) ? ", :image" : "") . ")";

        $params = [
            ':titre'               => $data['titre'],
            ':description'         => $data['description'],
            ':prix'                => $data['prix'],
            ':stock'               => $data['stock'],
            ':min_pers'            => $data['min_personnes'],
            ':theme_id'            => $data['theme_id'],
            ':regime_id'           => $data['regime_id'],
            ':composition'         => $data['composition'],
            ':conditions_stockage' => $data['conditions_stockage'],
        ];

        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->pdo->lastInsertId();
    }

    public function updateMenu(array $data)
    {
        $sql = "UPDATE menu 
            SET titre = :titre, 
                description = :description, 
                prix_par_personne = :prix, 
                quantite_restante = :stock, 
                nombre_personne_minimum = :min_pers,
                theme_id = :theme_id, 
                regime_id = :regime_id, 
                composition = :composition, 
                conditions_stockage = :conditions_stockage"
            . (!empty($data['image']) ? ", image = :image" : "") . " 
            WHERE menu_id = :id";

        $params = [
            ':titre'               => $data['titre'],
            ':description'         => $data['description'],
            ':prix'                => $data['prix'],
            ':stock'               => $data['stock'],
            ':min_pers'            => $data['min_personnes'],
            ':theme_id'            => $data['theme_id'],
            ':regime_id'           => $data['regime_id'],
            ':composition'         => $data['composition'],
            ':conditions_stockage' => $data['conditions_stockage'],
            ':id'                  => $data['menu_id']
        ];

        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function toggleStatus($menuId, $actif)
    {
        $stmt = $this->pdo->prepare("UPDATE menu SET actif = :actif WHERE menu_id = :id");
        $stmt->execute([
            ':actif' => $actif,
            ':id'    => $menuId
        ]);
        return $stmt->rowCount();
    }
}
