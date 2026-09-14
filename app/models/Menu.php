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

    public function getMenusByIds(array $menuIds): array
    {
        if (empty($menuIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($menuIds), '?'));
        $stmt = $this->pdo->prepare("SELECT menu_id, titre FROM menu WHERE menu_id IN ($placeholders)");
        $stmt->execute($menuIds);

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['menu_id']] = $row['titre'];
        }
        return $result;
    }

    public function getAllMenusTitres(): array
    {
        $stmt = $this->pdo->query("SELECT menu_id, titre FROM menu ORDER BY titre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère tous les menus avec les libellés de thème et régime
    public function getAllWithLabels(): array
    {
        $stmt = $this->pdo->query("
        SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle 
        FROM menu m
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        ORDER BY m.menu_id DESC
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère tous les menus actifs avec les libellés de thème et régime pour le ront-end
    public function getAllActiveWithLabels(): array
    {
        $stmt = $this->pdo->query("
        SELECT m.*, t.libelle AS theme_libelle, r.libelle AS regime_libelle 
        FROM menu m
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        WHERE m.actif = 1
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
