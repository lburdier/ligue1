<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté et a les droits d'administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit();
}

// Inclure les fichiers nécessaires pour la base de données et la gestion des articles
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionArticle.php'; // Ajouter la gestion des articles si elle existe

// Connexion à la base de données
$gestionBDD = new GestionBDD();
$pdo = $gestionBDD->connect();

if (!$pdo) {
    die("Erreur de connexion à la base de données.");
}

// Gestion des soumissions du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = !empty($_POST['titre']) ? htmlspecialchars(trim($_POST['titre'])) : '';
    $contenu = !empty($_POST['contenu']) ? htmlspecialchars(trim($_POST['contenu'])) : '';
    $categorie = !empty($_POST['categorie']) ? htmlspecialchars(trim($_POST['categorie'])) : '';

    // Gestion de l'image
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $image);
    }

    // Vérifier les champs obligatoires
    if ($titre && $contenu && $categorie) {
        // Instancier la classe de gestion d'articles
        $gestionArticle = new GestionArticle($pdo);
        
        // Créer l'article
        $gestionArticle->createArticle($titre, $contenu, $categorie, $image);

        echo "<script>alert('Article créé avec succès !'); window.location.href='/admin_dashboard';</script>";
    } else {
        echo "<script>alert('Veuillez remplir tous les champs obligatoires.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un article - Admin</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Créer un article</h1>
        <form action="admin_create_article.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre de l'article</label>
                <input type="text" class="form-control" id="titre" name="titre" placeholder="Saisir le titre de l'article" required>
            </div>

            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu de l'article</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="6" placeholder="Saisir le contenu de l'article" required></textarea>
            </div>

            <div class="mb-3">
                <label for="categorie" class="form-label">Catégorie</label>
                <select class="form-select" id="categorie" name="categorie" required>
                    <option value="" disabled selected>Choisir une catégorie</option>
                    <option value="Sport">Sport</option>
                    <option value="Culture">Culture</option>
                    <option value="Politique">Politique</option>
                    <option value="Économie">Économie</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image (facultatif)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Créer l'article</button>
        </form>
    </div>
</body>
</html>