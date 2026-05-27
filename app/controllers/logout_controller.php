<?php
session_start(); // On récupère la session en cours
session_unset(); // On vide les variables de session
session_destroy(); // On détruit le fichier de session sur le serveur
// OPTIONNEL: Supprimer le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
// Redirection vers la page d'accueil via le routeur
header("");
exit();
?>