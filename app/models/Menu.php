<?php
class Menu {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer un menu par son ID
    public function getMenuById($menuId) {
        $stmt = $this->pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
        $stmt->execute([$menuId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
}