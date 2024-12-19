<?php

// Démarrer la session uniquement si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la classe de gestion de la base de données et la classe Commentaire
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Commentaire.php';
include_once __DIR__ . '/../Models/GestionArticle.php';
include_once __DIR__ . '/../Models/Article.php';
// Initialiser la connexion à la base de données
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
} catch (Exception $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    echo "<p>Erreur : Impossible de se connecter à la base de données.</p>";
    exit;
}

// Vérifier si l'utilisateur est connecté
$utilisateurConnecte = isset($_SESSION['user']);
$nomUtilisateur = $utilisateurConnecte ? $_SESSION['user']['nom'] : 'Invité';
$imageProfil = $utilisateurConnecte ? $_SESSION['user']['image'] : 'avatars/default_avatar.png';

// Récupérer l'ID de l'article à partir de l'URL
$idArticle = isset($_GET['id']) ? (int) $_GET['id'] : 1;

// Récupérer l'article depuis la base de données
try {
    $query = $cnx->prepare("SELECT * FROM article WHERE id_article = :id");
    $query->execute(['id' => $idArticle]);
    $article = $query->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'article a été trouvé
    if (!$article) {
        echo "<p>Erreur : L'article demandé est introuvable.</p>";
        exit;
    }
} catch (Exception $e) {
    error_log("Erreur lors de la récupération de l'article : " . $e->getMessage());
    echo "<p>Erreur : Impossible de récupérer l'article.</p>";
    exit;
}

// Récupérer les commentaires associés à l'article
$commentaires = Commentaire::getCommentairesByArticleId($cnx, $idArticle);

// Récupérer la liste des articles pour l'affichage dans la vue
$articles = [];
try {
    $queryArticles = $cnx->query("SELECT * FROM article ORDER BY date_creation DESC");
    $articles = $queryArticles->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des articles : " . $e->getMessage());
    $articles = [];
}

// Inclure la vue de l'article
include __DIR__ . '/../Vues/v_article.php';

?>