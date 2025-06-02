<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionArticle.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Utiliser GestionArticle pour récupérer la liste des articles
    $gestionArticle = new GestionArticle($cnx);
    $articles = $gestionArticle->getAllArticles();

    // Vérifier si des articles ont été trouvés
    if (empty($articles)) {
        error_log("Aucun article trouvé dans la base de données.");
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des articles : " . $e->getMessage());
    $articles = [];
}

// Inclure la vue pour afficher les articles
include __DIR__ . '/../Vues/v_article.php';

?>