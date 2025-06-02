<?php
// Démarrage sécurisé de session + token CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialisation de $articles pour éviter des erreurs
$articles = $articles ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
    <style>
        .article-item {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .article-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .article-item img {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .article-content h3 {
            color: #1f3c88;
            transition: color 0.3s;
        }

        .article-content h3:hover {
            color: #1554b4;
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
            text-align: center;
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Liste des Articles</h2>
        <div class="row g-4">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $articleItem): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="article-item card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($articleItem['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                 onerror="this.onerror=null; this.src='/ligue1/img/placeholder.jpg';"
                                 alt="Image de l'article" class="card-img-top img-fluid">

                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="card-title h5 text-center">
                                        <?php echo htmlspecialchars($articleItem['titre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <p class="card-text text-center">
                                        <?php echo nl2br(htmlspecialchars(substr($articleItem['contenu'], 0, 150), ENT_QUOTES, 'UTF-8')); ?>...
                                    </p>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="/ligue1/voir_article?id=<?php echo urlencode($articleItem['id_article']); ?>" class="btn btn-primary">
                                        Lire la suite
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-article">Aucun article disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
