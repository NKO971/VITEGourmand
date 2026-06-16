<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /* On s'assure que l'utilisateur a un rôle 'utilisateur' (ID 3) et que son email n'existe pas déjà en DB */
    public function register($nom, $prenom, $email, $gsm, $adresse, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                // L'email existe déjà, on stoppe et on renvoie false
                return false; 
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $role_id = 3;

            $sql = "INSERT INTO utilisateur (nom, prenom, email, gsm, adresse, password, role_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $insert = $this->pdo->prepare($sql);
            
            return $insert->execute([$nom, $prenom, $email, $gsm, $adresse, $hashedPassword, $role_id]);

        } catch (PDOException $e) {
            // Étape de diagnostic : On stocke l'erreur réelle en session pour que le contrôleur l'affiche
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $_SESSION['error_sql'] = "Erreur DB : " . $e->getMessage();
            return false;
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Renvoie un tableau contenant les infos ou false si rien trouvé
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateProfile($userId, $nom, $prenom, $gsm, $adresse) {
        $sql = "UPDATE utilisateur 
                SET nom = ?, prenom = ?, gsm = ?, adresse = ? 
                WHERE utilisateur_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$nom, $prenom, $gsm, $adresse, $userId]);
        if (!$result) {
        var_dump($stmt->errorInfo()); // Affiche l'erreur SQL à l'écran
        die(); 
    }
    return $result;
    }
}