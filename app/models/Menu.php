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
        $sql = "INSERT INTO menu (titre, description, prix_par_personne, quantite_restante, nombre_personne_minimum, theme_id, regime_id, composition, conditions_stockage, delai_commande_valeur, delai_commande_unite, actif"
            . (!empty($data['image']) ? ", image" : "") . ")
            VALUES (:titre, :description, :prix, :stock, :min_pers, :theme_id, :regime_id, :composition, :conditions_stockage, :delai_valeur, :delai_unite, 1"
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
            ':delai_valeur'        => $data['delai_commande_valeur'] ?? null,
            ':delai_unite'         => $data['delai_commande_unite'] ?? null,
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
                conditions_stockage = :conditions_stockage,
                delai_commande_valeur = :delai_valeur,
                delai_commande_unite = :delai_unite"
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
            ':delai_valeur'        => $data['delai_commande_valeur'] ?? null,
            ':delai_unite'         => $data['delai_commande_unite'] ?? null,
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

    // Décrémente la quantité restante d'un menu de 1 si elle est supérieure à 0.
    public function decrementerStock(int $menuId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - 1 WHERE menu_id = :id AND quantite_restante > 0");
        $stmt->execute([':id' => $menuId]);
        return $stmt->rowCount() > 0;
    }

    // Incrémente la quantité restante d'un menu de 1.
    public function incrementerStock(int $menuId): void
    {
        $stmt = $this->pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante + 1 WHERE menu_id = :id");
        $stmt->execute([':id' => $menuId]);
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

    // ─────────────────────────────────────────────
    // Galerie d'images par menu (table menu_images)
    // URLs externes (pas d'upload fichier : filesystem
    // éphémère sur Heroku). menu.image reste la vignette.
    // ─────────────────────────────────────────────

    /**
     * Retourne les URLs de la galerie d'un menu, triées par ordre.
     * @return string[]
     */
    public function getImagesByMenuId($menuId): array
    {
        $stmt = $this->pdo->prepare("SELECT image_url FROM menu_images WHERE menu_id = ? ORDER BY ordre ASC, id ASC");
        $stmt->execute([(int)$menuId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Retourne les galeries de plusieurs menus en une seule requête.
     * @return array [menu_id => [url, ...]]
     */
    public function getAllImagesByMenuIds(array $menuIds): array
    {
        if (empty($menuIds)) {
            return [];
        }

        $ids = array_values(array_unique(array_map('intval', $menuIds)));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT menu_id, image_url FROM menu_images WHERE menu_id IN ($placeholders) ORDER BY menu_id ASC, ordre ASC, id ASC"
        );
        $stmt->execute($ids);

        $result = [];
        foreach ($ids as $id) {
            $result[$id] = [];
        }
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[(int)$row['menu_id']][] = $row['image_url'];
        }
        return $result;
    }

    /**
     * Remplace toute la galerie d'un menu par la liste d'URLs fournie.
     * Les URLs vides / invalides sont ignorées (max 10).
     * @param string[] $urls
     */
    public function saveGallery(int $menuId, array $urls): void
    {
        $clean = [];
        foreach ($urls as $url) {
            $url = trim((string)$url);
            if ($url === '' || strlen($url) > 500) {
                continue;
            }
            // N'accepte que des URLs http(s) — évite javascript: et chemins locaux
            if (!preg_match('#^https?://#i', $url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }
            if (!in_array($url, $clean, true)) {
                $clean[] = $url;
            }
            if (count($clean) >= 10) {
                break;
            }
        }

        $this->pdo->prepare("DELETE FROM menu_images WHERE menu_id = ?")->execute([$menuId]);

        if (empty($clean)) {
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO menu_images (menu_id, image_url, ordre) VALUES (?, ?, ?)");
        $ordre = 0;
        foreach ($clean as $url) {
            $stmt->execute([$menuId, $url, $ordre++]);
        }
    }

    /**
     * Découpe le contenu d'un textarea "une URL par ligne" en tableau d'URLs.
     * @return string[]
     */
    public static function parseGalleryTextarea(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $urls = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $urls[] = $line;
            }
        }
        return $urls;
    }

    /**
     * Calcule la date la plus proche a laquelle une prestation peut etre
     * reservee pour un menu ayant un delai de commande (valeur + unite
     * 'heures'|'jours'). Reproduit exactement calculerDateMinimum() de
     * public/js/commande.js (granularite jour : les heures sont arrondies
     * au jour superieur) afin que la validation serveur et la validation
     * cote client soient strictement coherentes.
     * Retourne null si le menu n'a pas de delai de commande structure.
     */
    public static function computeMinDateForDelai(?int $valeur, ?string $unite): ?DateTimeImmutable
    {
        if (empty($valeur) || $valeur <= 0) {
            return null;
        }

        $heuresTotales = ($unite === 'jours') ? $valeur * 24 : $valeur;
        $joursMinimum = (int)ceil($heuresTotales / 24);

        $today = new DateTimeImmutable('today');
        return $today->modify("+{$joursMinimum} days");
    }
}
