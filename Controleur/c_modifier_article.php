<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Article.php';
include_once __DIR__ . '/../Models/GestionArticle.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    echo "<p>Erreur : Vous devez être connecté pour modifier un article.</p>";
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
$idArticle = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id_article']) ? (int) $_POST['id_article'] : null);

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
            $fileTmpPath = $_FILES['upload_image']['tmp_name'];
            $fileName = basename($_FILES['upload_image']['name']); // Utilisation de basename pour sécuriser le nom de fichier
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = 'image_' . uniqid() . '.' . $fileExtension;

            $uploadDir = __DIR__ . '/../img/';
            $destPath = $uploadDir . $newFileName;

            // Vérifier l'extension de fichier pour des raisons de sécurité
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExtensions)) {
                // Vérifier le type MIME pour des raisons de sécurité
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($fileTmpPath);
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($mimeType, $allowedMimeTypes)) {
                    // Vérifier la taille du fichier (limite par exemple à 2 Mo)
                    if ($_FILES['upload_image']['size'] <= 2 * 1024 * 1024) {
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Supprimer l'ancienne image si elle existe et est différente
                            if ($article->getImage() && $article->getImage() !== $image) {
                                $oldImagePath = __DIR__ . '/../' . $article->getImage();
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            $image = 'img/' . $newFileName; // Met à jour le chemin de l'image
                        } else {
                            echo "<p>Erreur : Impossible de déplacer l'image uploadée.</p>";
                        }
                    } else {
                        echo "<p>Erreur : La taille de l'image dépasse la limite autorisée (2 Mo).</p>";
                    }
                } else {
                    echo "<p>Erreur : Type de fichier non autorisé.</p>";
                }
            } else {
                echo "<p>Erreur : Format de fichier non autorisé.</p>";
            }
        }

        // Vérifier que les champs obligatoires ne sont pas vides
        if (empty($titre) || empty($contenu) || empty($categorie)) {
            echo "<p>Erreur : Tous les champs obligatoires doivent être remplis.</p>";
        } else {
            // Mettre à jour l'article
            try {
                $result = $gestionArticle->updateArticle($idArticle, $titre, $contenu, $categorie, $image, $_SESSION['user']['id']);
                if ($result) {
                    // Rediriger vers la page de l'article après la mise à jour
                    header("Location: /ligue1/voir_article?id=" . urlencode($idArticle));
                    exit;
                } else {
                    echo "<p>Erreur lors de la mise à jour de l'article. Aucune modification effectuée.</p>";
                }
            } catch (Exception $e) {
                error_log("Erreur lors de la mise à jour de l'article : " . $e->getMessage());
                echo "<p>Erreur : Une erreur est survenue lors de la mise à jour de l'article.</p>";
            }
        }
    }

    // Inclure la vue pour afficher le formulaire de modification
    include __DIR__ . '/../Vues/v_modifier_article.php';
} else {
    echo "<p>Erreur : Aucun ID d'article spécifié ou ID invalide.</p>";
    exit;
}
?>