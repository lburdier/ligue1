<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
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
                flex-direction: column;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                border: 1px solid #ddd;
                margin-bottom: 20px;
                overflow: hidden;
            }

            .article-item img {
                width: 200px;
                height: 200px;
                border-radius: 8px;
                margin-right: 15px;
                object-fit: cover;
            }

            .card-body {
                display: flex;
                flex-direction: column;
                padding: 15px;
            }

            .card-title, .article-content h3 {
                font-size: 1.25rem;
                margin-bottom: 10px;
            }

            .card-text, .article-content p {
                margin-bottom: 15px;
            }

            .article-content {
                flex: 1;
            }

            .article-content a {
                text-decoration: none;
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

            form button, .btn-primary {
                background-color: #292929;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            form button:hover, .btn-primary:hover {
                background-color: #4d4d4d;
            }

            .btn btn-link text-primary {
                background-color: #fff;
            }

            .container h2 {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container mt-5">
            <div class="jumbotron bg-light p-5 rounded-lg custom-jumbotron text-right">
                <h1 class="display-4">Bienvenue sur Ligue1</h1>
                <p class="lead">Cette plateforme vous permet de gérer vos inscriptions, consulter les informations des clubs, et bien plus encore.</p>
                <hr class="my-4">
                <p>Utilisez le menu pour naviguer dans les différentes sections de l'application.</p>
                <div class="d-flex justify-content-right">
                    <a class="btn btn-primary btn-lg me-2" href="/ligue1/inscription" role="button">S'inscrire</a>
                    <a class="btn btn-primary btn-lg me-2" href="/ligue1/clubs" role="button">Voir les clubs</a>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h2 class="mb-4">Liste des articles</h2>
            <div class="row g-4">
                <?php if ($articles): ?>
                    <?php foreach ($articles as $articleItem): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="article-item border p-3 rounded shadow-sm bg-white">
                                <img src="<?php echo htmlspecialchars($articleItem['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image de l'article" class="img-fluid rounded mb-3">
                                <div class="article-content">
                                    <h3 class="h5"><?php echo htmlspecialchars($articleItem['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars(substr($articleItem['contenu'], 0, 150), ENT_QUOTES, 'UTF-8')); ?>...</p>
                                    <a href="/ligue1/voir_article?id=<?php echo urlencode($articleItem['id_article']); ?>">Lire la suite</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-article">Aucun article disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container mt-5">
            <h2 class="mb-4">Liste des clubs</h2>
            <div class="row">
                <?php if (!empty($tab)): ?>
                    <?php foreach ($tab as $club): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($club->getNom()); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($club->getEmplacement()); ?></h6>
                                    <p class="card-text"><?php echo htmlspecialchars($club->getLigue()); ?></p>
                                    <div class="mt-auto">
                                        <a href="#" class="btn btn-primary">Voir Détails</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            Aucun club trouvé.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>