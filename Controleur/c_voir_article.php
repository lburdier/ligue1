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

    // Vérifier et valider l'ID de l'article
    $idArticle = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$idArticle) {
        echo "<p>Erreur : Aucun ID d'article spécifié ou ID invalide.</p>";
        exit;
    }

    // Récupérer l'article par ID
    $gestionArticle = new GestionArticle($cnx);
    $article = $gestionArticle->getArticleById($idArticle);
    if (!$article) {
        echo "<p>Erreur : L'article demandé est introuvable.</p>";
        exit;
    }

    // Récupérer les commentaires associés à l'article
    $query = "SELECT auteur, contenu, date_commentaire FROM commentaire WHERE id_article = :id_article ORDER BY date_commentaire DESC";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_article', $idArticle, PDO::PARAM_INT);
    $stmt->execute();
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération de l'article ou des commentaires : " . $e->getMessage());
    echo "<p>Erreur : Une erreur est survenue lors de la récupération des données.</p>";
    exit;
}

// Inclure la vue pour afficher l'article
include __DIR__ . '/../Vues/v_voir_article.php';
?>
