<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifier l'article - <?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></title>
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
                border-radius: 8px;
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
                border: 1px solid #ccc;
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

            /* Style de base pour le formulaire */
            .responsive-form {
                width: 100%;
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .responsive-form label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333;
            }

            .responsive-form input[type="text"],
            .responsive-form textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
                font-size: 1rem;
                color: #333;
            }

            .responsive-form textarea {
                resize: vertical;
            }

            .responsive-form button {
                background-color: #141414;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1rem;
                transition: background-color 0.3s;
            }

            .responsive-form button:hover {
                background-color: #434343;
            }

            .cancel-button {
                padding: 10px;
                margin-top: 10px;
                background-color: #ffe0e0 ;
                color: #e63946 !important;
                border: 1px solid #e63946 !important;
                border-radius: 5px;
            }

            .cancel-button.dark-mode {
                padding: 10px;
                margin-top: 10px;
                background-color: #333 !important;
                color: #e0e0e0 !important;
                border: 1px solid #444 !important;
                border-radius: 5px;
            }

            .cancel-button:hover {
                color: #0056b3;
                text-decoration: underline;
            }
        </style>
    </head>

    <body>
        <div class="container_form">
            <h2>Modifier l'article</h2>
            <form method="post" action="/ligue1/c_update_article.php" class="responsive-form" enctype="multipart/form-data">
                <input type="hidden" name="id_article" value="<?php echo $article->getId(); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Titre -->
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Catégorie -->
                <label for="categorie">Catégorie :</label>
                <input type="text" id="categorie" name="categorie" required value="<?php echo htmlspecialchars($article->getCategorie(), ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Uploader une nouvelle image -->
                <label for="upload_image">Uploader une nouvelle image :</label>
                <input type="file" id="upload_image" name="upload_image" accept="image/*">

                <!-- Contenu -->
                <label for="contenu">Contenu :</label>
                <textarea id="contenu" name="contenu" rows="8" required><?php echo htmlspecialchars($article->getContenu(), ENT_QUOTES, 'UTF-8'); ?></textarea>

                <!-- Boutons -->
                <button type="submit">Enregistrer les modifications</button>
                <a href="/ligue1/voir_article?id=<?php echo $article->getId(); ?>" class="cancel-button">Annuler</a>
            </form>

        </div>
        <!-- Script pour la sauvegarde automatique -->
        <!-- Script pour la sauvegarde automatique -->
        <script>
            // Fonction pour sauvegarder les données du formulaire dans le localStorage
            function saveFormData() {
                const titre = document.getElementById('titre').value;
                const categorie = document.getElementById('categorie').value;
                const image = document.getElementById('image').value;
                const contenu = document.getElementById('contenu').value;

                // Stocker les valeurs dans le localStorage
                localStorage.setItem('titre', titre);
                localStorage.setItem('categorie', categorie);
                localStorage.setItem('image', image);
                localStorage.setItem('contenu', contenu);
            }

            // Fonction pour charger les données du formulaire depuis le localStorage
            function loadFormData() {
                if (localStorage.getItem('titre')) {
                    document.getElementById('titre').value = localStorage.getItem('titre');
                }
                if (localStorage.getItem('categorie')) {
                    document.getElementById('categorie').value = localStorage.getItem('categorie');
                }
                if (localStorage.getItem('image')) {
                    document.getElementById('image').value = localStorage.getItem('image');
                }
                if (localStorage.getItem('contenu')) {
                    document.getElementById('contenu').value = localStorage.getItem('contenu');
                }
            }

            // Charger les données du formulaire lors du chargement de la page
            window.onload = loadFormData;

            // Sauvegarder les données du formulaire à chaque modification
            document.getElementById('titre').addEventListener('input', saveFormData);
            document.getElementById('categorie').addEventListener('input', saveFormData);
            document.getElementById('image').addEventListener('input', saveFormData);
            document.getElementById('contenu').addEventListener('input', saveFormData);
        </script>
    </body>
</html>