<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Message de retour</title>
        <link rel="stylesheet" href="style/style.css">
        <style>
            .container {
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f4f4f9;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            .success-message {
                color: #28a745;
                font-size: 1.5em;
                margin-bottom: 20px;
            }
            .error-message {
                color: #dc3545;
                font-size: 1.2em;
                margin-bottom: 20px;
            }
            .back-link {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background-color: #007bff;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .back-link:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php if (isset($erreur) && !empty($erreur)): ?>
                <div class="error-message"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php else: ?>
                <p class="success-message">Votre commentaire a été ajouté avec succès !</p>
            <?php endif; ?>
            <a href="/ligue1/article" class="btn btn-secondary mt-4">Retour à l'article</a>
        </div>
    </body>
</html>