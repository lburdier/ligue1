<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    echo "<div style='
        margin: 50px auto;
        max-width: 600px;
        padding: 20px;
        border: 2px solid #dc3545;
        border-radius: 8px;
        background-color: #f8d7da;
        color: #721c24;
        font-family: Arial, sans-serif;
        text-align: center;
    '>
        <h2>🚫 Accès refusé</h2>
        <p>Vous devez être connecté pour créer un article.</p>
        <a href='/ligue1/connexion' style='
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        '>Se connecter</a>
        <a href='/ligue1/inscription' style='
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        '>S'inscrire</a>
    </div>";
    exit;
}

// Vérifier si l'utilisateur a le rôle de créateur
if ($_SESSION['user']['role'] !== 'createur') {
    echo "<div style='
        margin: 50px auto;
        max-width: 600px;
        padding: 20px;
        border: 2px solid #dc3545;
        border-radius: 8px;
        background-color: #f8d7da;
        color: #721c24;
        font-family: Arial, sans-serif;
        text-align: center;
    '>
        <h2>🚫 Accès refusé</h2>
        <p>Vous devez être connecté en tant que rédacteur d'articles pour créer un article.</p>
        <a href='/ligue1/connexion' style='
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        '>Se connecter</a>
    </div>";
    exit;
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionArticle.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionArticle = new GestionArticle($cnx);

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $categorie = trim($_POST['categorie'] ?? '');
        $image = $_FILES['image'] ?? null;
        $generatedImageUrl = trim($_POST['generated_image_url'] ?? ''); // URL de l'image générée
        $idUtilisateur = $_SESSION['user']['id'];

        // Validation des champs
        if (empty($titre) || empty($contenu) || empty($categorie)) {
            echo "<p>Erreur : Tous les champs sont requis.</p>";
        } else {
            // Vérifier si la catégorie existe déjà
            $query = "SELECT COUNT(*) FROM categories WHERE nom_categorie = :categorie";
            $stmt = $cnx->prepare($query);
            $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
            $stmt->execute();
            $categoryExists = $stmt->fetchColumn() > 0;

            // Ajouter la catégorie si elle n'existe pas
            if (!$categoryExists) {
                $queryInsert = "INSERT INTO categories (nom_categorie) VALUES (:categorie)";
                $stmtInsert = $cnx->prepare($queryInsert);
                $stmtInsert->bindParam(':categorie', $categorie, PDO::PARAM_STR);
                $stmtInsert->execute();
            }

            // Télécharger l'image ou utiliser l'image générée
            $imagePath = $image ? $gestionArticle->uploadImageArticle($image) : $generatedImageUrl;

            // Créer l'article
            $success = $gestionArticle->createArticle($titre, $contenu, $categorie, $imagePath, $idUtilisateur);

            if ($success) {
                echo "<div style='
                    margin: 50px auto;
                    max-width: 600px;
                    padding: 20px;
                    border: 2px solid #28a745;
                    border-radius: 8px;
                    background-color: #d4edda;
                    color: #155724;
                    font-family: Arial, sans-serif;
                    text-align: center;
                '>
                    <h2>✅ Succès</h2>
                    <p>Votre article a été créé avec succès.</p>
                    <a href='/ligue1/article' style='
                        display: inline-block;
                        margin-top: 15px;
                        padding: 10px 20px;
                        background-color: #28a745;
                        color: #fff;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                    '>Voir les articles</a>
                </div>";
                exit;
            } else {
                echo "<p>Erreur : Une erreur est survenue lors de la création de l'article.</p>";
            }
        }
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la création de l'article : " . $e->getMessage());
    echo "<p>Erreur : Une erreur interne est survenue. Veuillez réessayer plus tard.</p>";
    exit;
}

// Inclure la vue pour afficher le formulaire de création d'article
include __DIR__ . '/../Vues/v_create_article.php';
?>