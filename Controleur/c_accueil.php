<?php

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionClubs.php';
include_once __DIR__ . '/../Models/GestionNews.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
include_once __DIR__ . '/../Models/GestionAbonner.php';
include_once __DIR__ . '/../Models/Club.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$gestionBDD = new GestionBDD("BD_ligue1");

try {
    $cnx = $gestionBDD->connect();

    $gc = new GestionClubs($cnx);
    $tab = $gc->getListClub();

    // Récupérer la liste des articles
    $articles = [];
    $queryArticles = $cnx->query("SELECT * FROM article ORDER BY date_creation DESC");
    $articles = $queryArticles->fetchAll(PDO::FETCH_ASSOC);

    $pageTitle = "Page accueil - Ligue1"; // Définir le titre de la page

    include __DIR__ . '/../Vues/v_accueil.php';

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
