<?php

// Démarrer le tampon de sortie
ob_start();

// Vérifiez si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Redirection via JavaScript si l'utilisateur n'est pas connecté
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit;
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Initialiser la base de données et récupérer les informations de l'utilisateur
$gestionBDD = new GestionBDD("BD_ligue1");
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

// Récupérer l'ID et le nom de l'utilisateur depuis la session
$id = $_SESSION['user']['id'] ?? null; // Assurez-vous que l'ID est stocké dans la session
$nom = $_SESSION['user']['nom'] ?? null; // Assurez-vous que le nom est stocké dans la session

// Vérification si l'ID et le nom sont bien définis
if ($id === null || $nom === null) {
    // En cas d'erreur, redirigez vers la page d'accueil
    echo "<script>window.location.href='/ligue1/';</script>";
    exit;
}

// Récupérer les informations de l'utilisateur par ID et nom
$utilisateur = $gestionUtilisateur->getUserByIdAndName($id, $nom);

// Vérifier si l'utilisateur existe
if (!$utilisateur) {
    // En cas d'erreur, redirigez vers la page d'accueil
    echo "<script>window.location.href='/ligue1/';</script>";
    exit;
}

// Inclure la vue pour afficher le profil
$pageTitle = "Mon Profil - Ligue1"; // Définir le titre de la page
include_once __DIR__ . '/../Vues/v_profil.php'; // Assurez-vous que ce chemin est correct

// Fin du tampon de sortie
ob_end_flush();
?>