<?php

// helper pour vérifier si l'utilisateur est connecté et a le rôle approprié
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?page=connexion");
        exit();
    }
}

// helper pour vérifier si l'utilisateur a un rôle spécifique
/** 
 * Vérifie que l'utilisateur est connecté ET a l'un des rôles autorisés.
 * Redirige vers la connexion si non connecté, ou bloque l'accès (403/redirection) si mauvais rôle.
 * @param array $allowedRoles Liste des role_id autorisés, ex: [1, 2]
 * @param bool $isAjax Si true, répond en JSON 403 au lieu de rediriger (pour les routes API)
 */
function requireRole(array $allowedRoles, bool $isAjax = false): void
{
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
        if ($isAjax) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès refusé.']);
            exit();
        }
        header("Location: ?page=connexion");
        exit();
    }
}