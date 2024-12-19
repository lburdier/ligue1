<?php
// Générer un jeton CSRF et le stocker dans la session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($article['titre'], ENT_QUOTES, 'UTF-8'); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="style/style.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f4f4f9;
            }

            .container_article {
                max-width: 800px;
                margin: 20 auto;
                padding: 20px;
            }

            .article-list {
                max-width: 800px;
                margin: 20px auto;
            }

            .article-item {
                display: flex;
                align-items: flex-start;
                margin-bottom: 20px;
                padding: 15px;
                border: 1px solid #ddd;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .article-item img {
                width: 200px;
                height: 200px;
                border-radius: 8px;
                margin-right: 15px;
                object-fit: cover;
            }

            .article-content {
                flex: 1;
            }

            .article-content h3 {
                margin: 0 0 10px;
                color: #333;
            }

            .article-content p {
                margin: 0 0 10px;
                color: #555;
            }

            .article-content a {
                text-decoration: none;
                color: #007bff;
                font-weight: bold;
            }

            .article-content a:hover {
                text-decoration: underline;
            }

            .no-article {
                color: #666;
                text-align: center;
                font-style: italic;
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

            form {
                margin-top: 20px;
                padding: 15px;
            }

            form label {
                font-weight: bold;
                margin-bottom: 5px;
                display: block;
            }

            form input[type="text"],
            form textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }

            form button {
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            form button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>

    <body>
        <div class="container_article article-list">
            <h2>Liste des articles</h2>
            <?php if ($articles): ?>
                <?php foreach ($articles as $articleItem): ?>
                    <div class="article-item">
                        <img src="<?php echo htmlspecialchars($articleItem['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image de l'article">
                        <div class="article-content">
                            <h3><?php echo htmlspecialchars($articleItem['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars(substr($articleItem['contenu'], 0, 150), ENT_QUOTES, 'UTF-8')); ?>...</p>
                            <a href="/ligue1/voir_article?id=<?php echo urlencode($articleItem['id_article']); ?>">Lire la suite</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-article">Aucun article disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </body>
</html>