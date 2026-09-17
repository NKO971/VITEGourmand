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
        $sql = "INSERT INTO commande (numero_commande, date_commande, date_prestation, heure_livraison, adresse_livraison, code_postal_livraison, prix_menu, nombre_personne, prix_livraison, statut, utilisateur_id, menu_id) 
        VALUES (:num, :date_cmd, :date_prest, :heure, :adresse, :cp, :prix_m, :nb_p, :prix_l, :statut, :u_id, :m_id)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':num'        => $data['numero_commande'],
            ':date_cmd'   => $data['date_commande'],
            ':date_prest' => $data['date_prestation'],
            ':heure'      => $data['heure_livraison'],
            ':adresse'    => $data['adresse_livraison'],
            ':cp'         => $data['code_postal_livraison'],
            ':prix_m'     => $data['prix_menu'],
            ':nb_p'       => $data['nombre_personne'],
            ':prix_l'     => $data['prix_livraison'],
            ':statut'     => $data['statut'],
            ':u_id'       => $data['utilisateur_id'],
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
        $stmt = $this->pdo->prepare("SELECT statut FROM commande WHERE commande_id = :c_id AND utilisateur_id = :u_id");
        $stmt->execute(['c_id' => $orderId, 'u_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order && $order['statut'] === 'En attente';
    }

    public function cancelOrder($orderId, $userId, $nouveauStatut)
    {
        // On verifie que la commande appartient bien a l'utilisateur
        // ET que le statut est bien 'En attente' avant de modifier
        $stmt = $this->pdo->prepare("SELECT menu_id FROM commande WHERE commande_id = :c_id AND utilisateur_id = :u_id AND statut = 'En attente'");
        $stmt->execute([':c_id' => $orderId, ':u_id' => $userId]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$commande) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE commande 
            SET statut = :statut 
            WHERE commande_id = :c_id AND utilisateur_id = :u_id AND statut = 'En attente'";

            $stmtUpdate = $this->pdo->prepare($sql);
            $stmtUpdate->execute([
                ':statut' => $nouveauStatut,
                ':c_id'   => $orderId,
                ':u_id'   => $userId
            ]);

            if ($stmtUpdate->rowCount() === 0) {
                $this->pdo->rollBack();
                return false;
            }

            require_once __DIR__ . '/Menu.php';
            $menuModel = new Menu($this->pdo);
            $menuModel->incrementerStock((int)$commande['menu_id']);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur annulation commande : " . $e->getMessage());
            return false;
        }
    }

    public function getOrderWithUserInfo($orderId)
    {
        $stmt = $this->pdo->prepare("
        SELECT c.*, u.email, u.prenom 
        FROM commande c
        JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
        WHERE c.commande_id = :id
    ");
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function changeOrdersStatusWithFollowUp($orderId, $nouveauStatut, $modeContact = null, $motif = null, $setRestitution = false)
    {
        try {
            $this->pdo->beginTransaction();

            $stmtInfo = $this->pdo->prepare("SELECT statut, menu_id FROM commande WHERE commande_id = :c_id");
            $stmtInfo->execute([':c_id' => $orderId]);
            $commandeActuelle = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            $sql = "UPDATE commande 
            SET statut = :statut,
                mode_contact = :mode_contact,
                motif_annulation = :motif";

            if ($setRestitution) {
                $sql .= ", restitution_materiel = 1";
            }

            $sql .= " WHERE commande_id = :c_id";

            $stmt1 = $this->pdo->prepare($sql);
            $stmt1->execute([
                ':statut'       => $nouveauStatut,
                ':mode_contact' => $modeContact,
                ':motif'        => $motif,
                ':c_id'         => $orderId
            ]);

            $stmt2 = $this->pdo->prepare("INSERT INTO suivi_commande(commande_id, statut, date_modification, date_suivi) VALUES (:c_id, :statut, NOW(), NOW())");
            $stmt2->execute([
                ':c_id'   => $orderId,
                ':statut' => $nouveauStatut
            ]);

            // Restitution du stock uniquement si la commande passe (pour la premiere fois) a Annulee
            if ($nouveauStatut === 'Annulée' && $commandeActuelle && $commandeActuelle['statut'] !== 'Annulée') {
                require_once __DIR__ . '/Menu.php';
                $menuModel = new Menu($this->pdo);
                $menuModel->incrementerStock((int)$commandeActuelle['menu_id']);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Erreur suivi commande : " . $e->getMessage());
            return false;
        }
    }



    public function getOrderFollowUp($orderId)
    {
        $sql = "SELECT statut, date_suivi FROM suivi_commande WHERE commande_id = :c_id ORDER BY date_suivi ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($orderId, $userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE commande_id = :c_id AND utilisateur_id = :u_id");
        $stmt->execute(['c_id' => $orderId, 'u_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrder($orderId, $userId, $data)
    {
        // Double vérification : la commande appartient à l'utilisateur et le statut est 'En attente'
        $sql = "UPDATE commande 
            SET date_prestation = :date_prest,
                heure_livraison = :heure,
                adresse_livraison = :adresse,
                code_postal_livraison = :cp,
                nombre_personne = :nb_p,
                prix_menu = :prix_m,
                prix_livraison = :prix_l
            WHERE commande_id = :c_id AND utilisateur_id = :u_id AND statut = 'En attente'";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':date_prest' => $data['date_prestation'],
            ':heure'      => $data['heure_livraison'],
            ':adresse'    => $data['adresse_livraison'],
            ':cp'         => $data['code_postal_livraison'],
            ':nb_p'       => $data['nombre_personne'],
            ':prix_m'     => $data['prix_menu'],
            ':prix_l'     => $data['prix_livraison'],
            ':c_id'       => $orderId,
            ':u_id'       => $userId
        ]);
    }

    public function searchOrders(?string $search = null, ?string $status = null, ?string $date = null): array
    {
        $sql = "SELECT 
            c.commande_id,
            c.numero_commande,
            c.date_commande,
            c.date_prestation,
            c.heure_livraison,
            c.prix_menu,
            c.nombre_personne,
            c.prix_livraison,
            (c.prix_menu + COALESCE(c.prix_livraison, 0)) AS montant_total,
            c.statut,
            c.pret_materiel,
            c.restitution_materiel,
            c.mode_contact,
            c.motif_annulation,
            u.nom,
            u.prenom,
            u.email,
            m.titre AS menu_titre
        FROM commande c
        JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
        LEFT JOIN menu m ON c.menu_id = m.menu_id
        WHERE 1=1";

        $params = [];

        if ($status) {
            $sql .= " AND c.statut = :status";
            $params[':status'] = $status;
        }

        if ($date) {
            $sql .= " AND DATE(c.date_commande) = :date";
            $params[':date'] = $date;
        }

        if ($search) {
            $sql .= " AND (
        u.nom LIKE :search 
        OR u.prenom LIKE :search 
        OR CONCAT(u.prenom, ' ', u.nom) LIKE :search
        OR CONCAT(u.nom, ' ', u.prenom) LIKE :search
        OR c.commande_id LIKE :search 
        OR c.numero_commande LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY c.date_commande DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculerTotalCommande($prixMenu, $nbPersonnes, $minPersonnes, $distanceKM)
    {
        if ($nbPersonnes < $minPersonnes) {
            return [
                'zone_desservie' => null,
                'nombre_suffisant' => false,
                'total_menu' => 0,
                'frais_livraison' => 0,
                'total_general' => 0
            ];
        }

        if ($distanceKM === null) {
            return [
                'zone_desservie' => false,
                'nombre_suffisant' => true,
                'total_menu' => 0,
                'frais_livraison' => 0,
                'total_general' => 0
            ];
        }

        $totalMenu = $prixMenu * $nbPersonnes;

        if ($nbPersonnes >= ($minPersonnes + 5)) {
            $totalMenu = $totalMenu * 0.9;
        }

        $fraisLivraison = ($distanceKM > 0) ? (5 + (0.59 * $distanceKM)) : 0;

        return [
            'zone_desservie' => true,
            'nombre_suffisant' => true,
            'total_menu' => $totalMenu,
            'frais_livraison' => $fraisLivraison,
            'total_general' => $totalMenu + $fraisLivraison
        ];
    }

    public function generateNumeroCommande(): string
    {
        return 'CMD-' . uniqid();
    }

    // Cette fonction calcule la date limite en jours ouvrables à partir d'une date donnée.
    public function calculateWorkingDaysDeadline(string $startDateStr, int $workingDays = 10): string
    {
        $date = new DateTime($startDateStr);
        $addedDays = 0;
        while ($addedDays < $workingDays) {
            $date->modify('+1 day');
            if ($date->format('N') < 6) { // Lundi à vendredi
                $addedDays++;
            }
        }
        return $date->format('d/m/Y');
    }

    // Cette fonction vérifie si la transition de statut est autorisée.
    public function isTransitionAutorisee(string $statutActuel, string $nouveauStatut): bool
    {
        $transitionsAutorisees = [
            'En attente'                       => ['En attente', 'Acceptée', 'Annulée'],
            'Acceptée'                          => ['Acceptée', 'En préparation', 'Annulée'],
            'En préparation'                    => ['En préparation', 'En cours de livraison'],
            'En cours de livraison'             => ['En cours de livraison', 'Livré'],
            'Livré'                              => ['Livré', 'En attente du retour de matériel', 'Terminée'],
            'En attente du retour de matériel'  => ['En attente du retour de matériel', 'Terminée'],
            'Terminée'                          => ['Terminée'],
            'Annulée'                           => ['Annulée'],
        ];

        return in_array($nouveauStatut, $transitionsAutorisees[$statutActuel] ?? []);
    }
}
