<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article</title>
    <link rel="stylesheet" href="/ligue1/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Créer un Article</h2>
        <form action="/ligue1/create_article" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" id="titre" name="titre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea id="contenu" name="contenu" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <button type="button" id="generate-content" class="btn btn-secondary">Générer le contenu avec Mistral AI</button>
                <span id="loading-indicator" style="display: none; margin-left: 10px; color: #007bff;">Génération en cours...</span>
            </div>
            <div class="mb-3">
                <label for="categorie" class="form-label">Catégorie</label>
                <select id="categorie" name="categorie" class="form-select" required>
                    <option value="" disabled selected>Choisir une catégorie</option>
                    <option value="Sport">Sport</option>
                    <option value="Actualité">Actualité</option>
                    <option value="Technologie">Technologie</option>
                    <option value="Santé">Santé</option>
                    <option value="Culture">Culture</option>
                </select>
                <button type="button" id="suggest-category" class="btn btn-secondary mt-2">Suggérer une catégorie avec Mistral AI</button>
                <span id="category-loading-indicator" style="display: none; margin-left: 10px; color: #007bff;">Suggestion en cours...</span>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control">
                <button type="button" id="generate-image" class="btn btn-secondary mt-2">Générer une image avec Mistral AI</button>
                <span id="image-loading-indicator" style="display: none; margin-left: 10px; color: #007bff;">Génération en cours...</span>
                <div id="image-preview" class="mt-3" style="display: none;">
                    <p><strong>Aperçu de l'image générée :</strong></p>
                    <img id="generated-image" src="" alt="Image générée" style="max-width: 100%; height: auto; border: 1px solid #ccc; border-radius: 8px;">
                    <input type="hidden" id="generated-image-url" name="generated_image_url">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>

    <script>
        document.getElementById('generate-content').addEventListener('click', async () => {
            const titre = document.getElementById('titre').value.trim();
            const loadingIndicator = document.getElementById('loading-indicator');

            if (!titre) {
                alert('Veuillez entrer un titre pour générer le contenu.');
                return;
            }

            loadingIndicator.style.display = 'inline'; // Show the loading indicator

            try {
                const response = await fetch('/ligue1/api/generate_article.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ titre })
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById('contenu').value = data.content;
                } else {
                    alert('Erreur lors de la génération du contenu : ' + data.message);
                }
            } catch (error) {
                console.error('Erreur lors de la requête :', error);
                alert('Une erreur est survenue lors de la génération du contenu.');
            } finally {
                loadingIndicator.style.display = 'none'; // Hide the loading indicator
            }
        });

        document.getElementById('suggest-category').addEventListener('click', async () => {
            const contenu = document.getElementById('contenu').value.trim();
            const loadingIndicator = document.getElementById('category-loading-indicator');

            if (!contenu) {
                alert('Veuillez rédiger un contenu pour suggérer une catégorie.');
                return;
            }

            loadingIndicator.style.display = 'inline'; // Show the loading indicator

            try {
                const response = await fetch('/ligue1/api/suggest_category.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ contenu })
                });

                const data = await response.json();
                if (data.success) {
                    const categoryDropdown = document.getElementById('categorie');
                    const options = Array.from(categoryDropdown.options);

                    // Check if the suggested category exists in the dropdown
                    let matchingOption = options.find(option => option.value.toLowerCase() === data.category.toLowerCase());

                    if (!matchingOption) {
                        // Add the suggested category to the dropdown temporarily
                        const newOption = document.createElement('option');
                        newOption.value = data.category;
                        newOption.textContent = data.category;
                        categoryDropdown.appendChild(newOption);
                        matchingOption = newOption;
                    }

                    // Select the suggested category
                    categoryDropdown.value = matchingOption.value;
                } else {
                    alert('Erreur lors de la suggestion de catégorie : ' + data.message);
                }
            } catch (error) {
                console.error('Erreur lors de la requête :', error);
                alert('Une erreur est survenue lors de la suggestion de catégorie.');
            } finally {
                loadingIndicator.style.display = 'none'; // Hide the loading indicator
            }
        });

        document.getElementById('generate-image').addEventListener('click', async () => {
            const titre = document.getElementById('titre').value.trim();
            const contenu = document.getElementById('contenu').value.trim();
            const loadingIndicator = document.getElementById('image-loading-indicator');
            const imagePreview = document.getElementById('image-preview');
            const generatedImage = document.getElementById('generated-image');
            const generatedImageUrl = document.getElementById('generated-image-url');

            if (!titre && !contenu) {
                alert('Veuillez entrer un titre ou un contenu pour générer une image.');
                return;
            }

            loadingIndicator.style.display = 'inline'; // Show the loading indicator

            try {
                const response = await fetch('/ligue1/api/generate_image.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ titre, contenu })
                });

                const data = await response.json();
                if (data.success) {
                    generatedImage.src = data.image_url;
                    generatedImageUrl.value = data.image_url;
                    imagePreview.style.display = 'block'; // Show the image preview
                } else {
                    alert('Erreur lors de la génération de l\'image : ' + data.message);
                }
            } catch (error) {
                console.error('Erreur lors de la requête :', error);
                alert('Une erreur est survenue lors de la génération de l\'image.');
            } finally {
                loadingIndicator.style.display = 'none'; // Hide the loading indicator
            }
        });
    </script>
</body>
</html>