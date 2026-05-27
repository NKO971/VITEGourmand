<?php
// On vérifie si une session est déjà lancée, sinon on la lance
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fonction pour sécuriser une page selon un rôle précis
 * @param int $role_attendu (1 pour Admin, 2 pour Employé, etc.)
 */
function securiser_page($role_attendu) {
    // Si l'utilisateur n'est pas connecté OU n'a pas le bon rôle
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != $role_attendu) {
        // Redirection vers l'accueil avec un message d'erreur (optionnel)
        header("Location: /EcoRide/index.php?erreur=acces_interdit");
        exit();
    }
}
?>