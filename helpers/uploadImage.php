<?php

/**
 * Vérifie que le fichier uploadé est bien une image dans un format autorisé.
 * @return string|null Message d'erreur si invalide, null si valide.
 */
function validateImageMimeType(array $file): ?string
{
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileMimeType = mime_content_type($file['tmp_name']);

    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        return 'Format d\'image non autorisé (JPG, PNG, WEBP uniquement).';
    }
    return null;
}

/**
 * Valide et déplace une image uploadée vers le dossier de stockage sur disque.
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function moveUploadedImage(array $file, string $prefix): array
{
    $mimeError = validateImageMimeType($file);
    if ($mimeError) {
        return ['success' => false, 'path' => null, 'error' => $mimeError];
    }

    $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName   = $prefix . '_' . uniqid() . '.' . $extension;
    $uploadDir  = ROOT_PATH . 'public/assets/images/';
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'path' => null, 'error' => 'Échec du transfert de l\'image.'];
    }

    return ['success' => true, 'path' => 'assets/images/' . $fileName, 'error' => null];
}