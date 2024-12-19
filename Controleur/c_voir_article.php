<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF s'il n'existe pas déjà
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Article.php';
include_once __DIR__ . '/../Models/GestionArticle.php';
include_once __DIR__ . '/../Models/Commentaire.php';
include_once __DIR__ . '/../Models/Utilisateur.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
} catch (Exception $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    echo "<p>Erreur : Impossible de se connecter à la base de données.</p>";
    exit;
}

// Vérifier et valider l'ID de l'article
$idArticle = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idArticle) {
    echo "<p>Erreur : Aucun ID d'article spécifié ou ID invalide.</p>";
    exit;
}

// Récupérer l'article par ID
$article = Article::getArticleById($idArticle, $cnx);
if (!$article) {
    echo "<p>Erreur : L'article demandé est introuvable.</p>";
    exit;
}

// Récupérer le propriétaire de l'article
try {
    $query = $cnx->prepare("SELECT nom_uti, prenom_uti FROM utilisateur WHERE id_uti = :id_uti");
    $query->execute(['id_uti' => $article->getIdUtilisateur()]);
    $proprietaire = $query->fetch(PDO::FETCH_ASSOC);

    if (!$proprietaire) {
        error_log("Propriétaire de l'article introuvable pour ID : " . $article->getIdUtilisateur());
        echo "<p>Erreur : Propriétaire de l'article introuvable.</p>";
        exit;
    }
} catch (Exception $e) {
    error_log("Erreur lors de la récupération du propriétaire de l'article : " . $e->getMessage());
    echo "<p>Erreur : Une erreur est survenue lors de la récupération des informations du propriétaire.</p>";
    exit;
}

// Vérifier si un utilisateur est connecté
$gestionUtilisateur = new GestionUtilisateur($cnx);
$idUtilisateurConnecte = $gestionUtilisateur->getIdUtilisateurConnecte();

$nomUtilisateurConnecte = null;
$prenomUtilisateurConnecte = null;

if ($idUtilisateurConnecte !== null) {
    try {
        // Récupérer les informations de l'utilisateur connecté
        $userQuery = $cnx->prepare("SELECT nom_uti, prenom_uti FROM utilisateur WHERE id_uti = :id_uti");
        $userQuery->execute(['id_uti' => $idUtilisateurConnecte]);
        $utilisateurConnecte = $userQuery->fetch(PDO::FETCH_ASSOC);

        if ($utilisateurConnecte) {
            $nomUtilisateurConnecte = $utilisateurConnecte['nom_uti'];
            $prenomUtilisateurConnecte = $utilisateurConnecte['prenom_uti'];
        } else {
            error_log("Utilisateur connecté non trouvé dans la base de données pour ID : $idUtilisateurConnecte");
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des informations de l'utilisateur connecté : " . $e->getMessage());
    }
}

// Définir si l'utilisateur connecté est le propriétaire de l'article
$estProprietaire = ($idUtilisateurConnecte !== null && $idUtilisateurConnecte === $article->getIdUtilisateur());

// Récupérer les commentaires associés à l'article
try {
    $commentaires = Commentaire::getCommentairesByArticleId($cnx, $idArticle);
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des commentaires pour l'article ID $idArticle : " . $e->getMessage());
    $commentaires = [];
}

// Inclure la vue pour afficher l'article complet
include __DIR__ . '/../Vues/v_voir_article.php';
