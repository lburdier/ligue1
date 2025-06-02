<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Article.php';
include_once __DIR__ . '/../Models/GestionArticle.php';
include_once __DIR__ . '/c_blacklist.php'; // Inclusion du contrôleur de blacklist

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'rédacteur d\'articles') {
    echo "<p>Erreur : Vous devez être connecté en tant que rédacteur d'articles pour modifier un article.</p>";
    exit;
}

// Connexion à la base de données
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
} catch (Exception $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    echo "<p>Erreur : Impossible de se connecter à la base de données.</p>";
    exit;
}

// Vérifier l'ID de l'article dans la requête GET ou POST
$idArticle = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id_article']) ? (int)$_POST['id_article'] : null);

if ($idArticle) {
    $gestionArticle = new GestionArticle($cnx);
    $article = $gestionArticle->getArticleById($idArticle);

    if (!$article) {
        echo "<p>Erreur : L'article demandé est introuvable.</p>";
        exit;
    }

    // Vérifier si l'utilisateur connecté est le propriétaire de l'article
    if ($_SESSION['user']['id'] !== $article->getIdUtilisateur()) {
        echo "<p>Erreur : Vous n'êtes pas autorisé à modifier cet article.</p>";
        exit;
    }

    // Vérifier que les données du formulaire sont soumises
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = trim($_POST['titre']);
        $contenu = trim($_POST['contenu']);
        $categorie = trim($_POST['categorie']);
        $image = $article->getImage(); // Utiliser l'image existante par défaut

        // Gérer l'upload d'une nouvelle image
        if (isset($_FILES['upload_image']) && $_FILES['upload_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImagePath = $gestionArticle->uploadImageArticle($_FILES['upload_image']);
            if ($uploadedImagePath) {
                $image = $uploadedImagePath; // Met à jour le chemin de l'image pour la base de données
            } else {
                echo "<p>Erreur : Une erreur s'est produite lors de l'upload de l'image.</p>";
                exit;
            }
        }

        // Vérifier que les champs obligatoires ne sont pas vides
        if (empty($titre) || empty($contenu) || empty($categorie)) {
            echo "<p>Erreur : Tous les champs obligatoires doivent être remplis.</p>";
            exit;
        }

        // Vérification des mots interdits via le contrôleur `c_blacklist`
        $texteComplet = $titre . ' ' . $contenu . ' ' . $categorie; // Combiner toutes les entrées
        $motInterdit = verifierMotsInterdits($texteComplet);

        if ($motInterdit !== null) {
            echo "<p>Erreur : Votre article contient un mot interdit : '$motInterdit'. Veuillez le corriger avant de soumettre.</p>";
            exit;
        }

        // Mettre à jour l'article
        try {
            $result = $gestionArticle->updateArticle($idArticle, $titre, $contenu, $categorie, $image, $_SESSION['user']['id']);
            if ($result) {
                // Rediriger vers la page de l'article après la mise à jour
                echo "<script type='text/javascript'>window.location.href = '/ligue1/voir_article?id=" . urlencode($idArticle) . "';</script>";
                exit;
            } else {
                echo "<p>Erreur lors de la mise à jour de l'article. Aucune modification effectuée.</p>";
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour de l'article : " . $e->getMessage());
            echo "<p>Erreur : Une erreur est survenue lors de la mise à jour de l'article.</p>";
        }
    }

    // Inclure la vue pour afficher le formulaire de modification
    include __DIR__ . '/../Vues/v_modifier_article.php';
} else {
    echo "<p>Erreur : Aucun ID d'article spécifié ou ID invalide.</p>";
    exit;
}

?>