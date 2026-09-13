<?php

class PasswordReset
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Crée un token de réinitialisation de mot de passe pour un utilisateur donné et le stocke dans la base de données.
    public function createToken(int $utilisateurId): string
    {
        $token = bin2hex(random_bytes(32)); // Génère un token aléatoire sécurisé de 64 caractères hexadécimaux.
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        $sql = "INSERT INTO password_reset_tokens (utilisateur_id, token, expires_at) VALUES (:uid, :token, :expires)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid'     => $utilisateurId,
            ':token'   => $token,
            ':expires' => $expiresAt
        ]);

        return $token;
    }

    // Vérifie si le token est valide et retourne l'ID de l'utilisateur associé, ou null si invalide.
    public function verifyToken(string $token): ?int
    {
        $sql = "SELECT utilisateur_id FROM password_reset_tokens WHERE token = :token AND expires_at > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['utilisateur_id'] : null;
    }

    /**
     * Invalide (supprime) un token après usage — usage unique garanti.
     */
    public function deleteToken(string $token): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_reset_tokens WHERE token = :token");
        $stmt->execute([':token' => $token]);
    }
}