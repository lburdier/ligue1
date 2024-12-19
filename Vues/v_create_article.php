<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="style/style.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                line-height: 1.6;
                margin: 0;
                padding: 0;
            }

            .container_form {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
            }

            h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #333;
            }

            form {
                display: flex;
                flex-direction: column;
            }

            label {
                font-weight: bold;
                margin-bottom: 5px;
            }

            input[type="text"],
            textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 5px;
                border: 1px solid #ddd;
                box-sizing: border-box;
            }

            button {
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1rem;
                transition: background-color 0.3s;
            }

            button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>

    <body>
        <div class="container_form">
            <h2>Créer un nouvel article</h2>
            <form method="post" action="/ligue1/c_create_article.php" enctype="multipart/form-data">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" required>

                <label for="categorie">Catégorie :</label>
                <input type="text" id="categorie" name="categorie" required>

                <label for="upload_image">Uploader une image :</label>
                <input type="file" id="upload_image" name="upload_image" accept="image/*">

                <label for="contenu">Contenu :</label>
                <textarea id="contenu" name="contenu" rows="8" required></textarea>

                <button type="submit">Créer l'article</button>
            </form>
        </div>
    </body>
</html>