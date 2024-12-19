<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger les modèles nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Initialiser la base de données et les classes associées
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);
} catch (Exception $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    // Rediriger ou afficher un message d'erreur approprié
    die('Erreur de connexion à la base de données.');
}

// Vérifier si l'utilisateur est connecté
$utilisateurConnecte = isset($_SESSION['user']) && !empty($_SESSION['user']);
$nomUtilisateur = $utilisateurConnecte ? $_SESSION['user']['nom'] : 'Invité';
$imageProfil = $utilisateurConnecte ? $_SESSION['user']['image'] : 'avatars/default_avatar.png';

// Transmettre les données à la vue
$pageTitle = "Menu - Ligue1";

// Charger la vue
include __DIR__ . '/../menu.php';
?>