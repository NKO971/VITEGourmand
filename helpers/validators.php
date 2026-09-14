<?php

/**
 * Vérifie qu'un mot de passe respecte la politique de sécurité du site :
 * 10 caractères min., au moins 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.
 *
 * @return string|null Message d'erreur si invalide, null si le mot de passe est valide.
 */
function validatePasswordStrength(string $password): ?string
{
    if (strlen($password) < 10) {
        return 'Le mot de passe doit contenir au moins 10 caractères.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une lettre majuscule.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Le mot de passe doit contenir au moins une lettre minuscule.';
    }
    if (!preg_match('/\d/', $password)) {
        return 'Le mot de passe doit contenir au moins un chiffre.';
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return 'Le mot de passe doit contenir au moins un caractère spécial.';
    }
    return null;
}