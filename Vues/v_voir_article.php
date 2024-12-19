<?php
// Assurez-vous que la variable $idArticle est bien définie
$idArticle = isset($article) ? $article->getId() : null;

// Vérifier si les informations utilisateur sont disponibles
$userData = [
    'prenom' => $prenomUtilisateurConnecte ?? '',
    'nom' => $nomUtilisateurConnecte ?? ''
];
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="stylesheet" href="style/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f4f4f9;
            }

            .container_article {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .article-user.dark-mode {
                width: 100%; /* Prend toute la largeur de son conteneur parent */
                margin: 20px auto; /* Garde les mêmes marges que les autres blocs */
                padding: 20px;
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                background-color: #1e1e1e; /* Couleur de fond sombre */
                border: 1px solid #333; /* Couleur de bordure sombre */
                color: #e0e0e0; /* Couleur du texte claire */
                box-sizing: border-box; /* Assure que le padding est inclus dans la largeur */
            }

            .article-user {
                width: 100%; /* Prend toute la largeur de son conteneur parent */
                margin: 20px auto; /* Garde les mêmes marges que les autres blocs */
                padding: 20px;
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                background-color: #fff;
                border: 1px solid #ddd;
                box-sizing: border-box; /* Assure que le padding est inclus dans la largeur */
            }

            .back-button {
                background-color: #141414;
                color: #fff;
                padding: 10px 15px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1rem;
                transition: background-color 0.3s;
                margin-bottom: 15px;
            }

            .back-button:hover {
                background-color: #434343;
            }

            .article-header {
                margin-bottom: 15px;
            }

            .article-header h1 {
                color: #333;
                font-size: 1.8rem;
                margin: 0;
            }

            .article-header .category {
                font-size: 1rem;
                color: #007bff;
                margin-top: 5px;
            }

            .article-image-container {
                width: 100%;
                text-align: center;
                margin-bottom: 15px;
            }

            .article-image {
                width: 250px;
                height: 250px;
                border-radius: 8px;
                object-fit: cover;
            }

            .article-content {
                color: #555;
                margin-top: 15px;
            }

            .edit-button {
                background-color: #141414;
                color: #fff;
                padding: 10px 15px;
                border-radius: 5px;
                text-decoration: none;
                margin-bottom: 15px;
                display: inline-block;
                font-size: 1rem;
            }

            .edit-button:hover {
                background-color: #434343;
            }

            .commentaire {
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 15px;
                border-radius: 8px;
                background-color: #fff;
            }

            .no-comment {
                color: #666;
                font-style: italic;
                margin-top: 20px;
            }

            .responsive-form {
                width: 100%;
            }

            .responsive-form input[type="text"],
            .responsive-form textarea,
            .responsive-form button {
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 10px;
            }

            .responsive-form label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
        </style>
    </head>

    <body>
        <div class="container_article">
            <div class="article-user">
                <a href="/ligue1/article" class="back-button">Revenir en arrière</a>

                <?php if (isset($estProprietaire) && $estProprietaire): ?>
                    <a href="/ligue1/modifier_article?id=<?php echo urlencode($article->getId()); ?>" class="edit-button">Modifier l'article</a>
                <?php endif; ?>

                <div class="article-header">
                    <h1><?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="category"><strong>Catégorie :</strong> <?php echo htmlspecialchars($article->getCategorie(), ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <p><strong>Propriétaire :</strong> <?php echo htmlspecialchars($proprietaire['prenom_uti'] . ' ' . $proprietaire['nom_uti'], ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="article-image-container">
                    <img src="<?php echo htmlspecialchars($article->getImage(), ENT_QUOTES, 'UTF-8'); ?>" alt="Image de l'article" class="article-image">
                </div>

                <div class="article-content">
                    <p><?php echo nl2br(htmlspecialchars($article->getContenu(), ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
            </div>
        </div>

        <div class="container_article container_listes_commentaires">
            <h3 id="commentaires">Liste des commentaires</h3>
            <?php if ($commentaires): ?>
                <?php foreach ($commentaires as $commentaire): ?>
                    <div class="commentaire">
                        <strong><?php echo $commentaire->getAuteur(); ?></strong>
                        <em><?php echo $commentaire->getDateCommentaire(); ?></em>
                        <p><?php echo nl2br($commentaire->getContenu()); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-comment">Aucun commentaire disponible pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="container_article container_commentaires">
            <h2>Commentaires</h2>
            <form class="responsive-form" method="post" action="/ligue1/c_create_commentaire.php">
                <?php if (isset($idArticle)): ?>
                    <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($idArticle, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <?php if (!empty($userData['prenom']) && !empty($userData['nom'])): ?>
                    <input type="hidden" id="auteur" name="auteur" value="<?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom'], ENT_QUOTES, 'UTF-8'); ?>">
                    <p><strong>Auteur :</strong> <?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php else: ?>
                    <p><strong>Auteur :</strong> <em>Informations de l'utilisateur manquantes.</em></p>
                <?php endif; ?>

                <label for="contenu">Commentaire :</label>
                <textarea id="contenu" name="contenu" required minlength="5" maxlength="255"></textarea>
                <small>Votre commentaire doit contenir entre 5 et 255 caractères.</small>

                <button type="submit">Commenter</button>
            </form>
        </div>
    </body>
</html>
