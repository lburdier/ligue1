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
if (!isset($_SESSION['user'])) {
    echo "<p>Erreur : Vous devez être connecté pour créer un article.</p>";
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

$gestionArticle = new GestionArticle($cnx);

// Gestion des données du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $categorie = trim($_POST['categorie']);
    $image = null;

    // Vérification de l'image
    if (isset($_FILES['upload_image']) && $_FILES['upload_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['upload_image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 500 * 1024; // 500 Ko
        // Vérifier la taille du fichier
        if ($file['size'] > $maxFileSize) {
            echo "<p>Erreur : La taille du fichier ne doit pas dépasser 500 Ko.</p>";
            exit;
        }

        // Vérifier le type MIME réel du fichier
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimeTypes)) {
            echo "<p>Erreur : Le type de fichier est invalide.</p>";
            exit;
        }

        // Vérifier l'extension du fichier
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<p>Erreur : L'extension du fichier est invalide.</p>";
            exit;
        }

        // Tenter de télécharger l'image
        $uploadedImagePath = $gestionArticle->uploadImageArticle($file);
        if ($uploadedImagePath) {
            $image = $uploadedImagePath;
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
    $texteComplet = $titre . ' ' . $contenu . ' ' . $categorie; // Combiner toutes les entrées utilisateur
    $motInterdit = verifierMotsInterdits($texteComplet);

    if ($motInterdit !== null) {
        echo "<p>Erreur : Votre article contient un mot interdit : '$motInterdit'. Veuillez le corriger avant de soumettre.</p>";
        exit;
    }

    // Créer l'article
    try {
        $result = $gestionArticle->createArticle($titre, $contenu, $categorie, $image, $_SESSION['user']['id']);
        if ($result) {
            // Rediriger vers la page d'accueil après la création
            echo "<script type='text/javascript'>window.location.href = '/ligue1/article';</script>";
            exit;
        } else {
            echo "<p>Erreur lors de la création de l'article.</p>";
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la création de l'article : " . $e->getMessage());
        echo "<p>Erreur : Une erreur est survenue lors de la création de l'article.</p>";
    }
}

// Inclure la vue pour afficher le formulaire de création
include __DIR__ . '/../Vues/v_create_article.php';
?>