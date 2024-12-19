<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

// Détruire la session
$_SESSION = []; // Vide la session
session_destroy(); // Détruit la session

header('Location: /ligue1'); // Rediriger vers la page d'accueil
exit; // Terminer le script
