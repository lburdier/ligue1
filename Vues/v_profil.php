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
    </head>
    <style>
        .img-thumbnail {
            padding: .25rem;
            background-color: #f8f9fa;
            border: 1px solid #6c757d;
            border-radius: 54.0rem;
            max-width: 100%;
            height: auto;
        }

        #preview-image {
            display: block;
            padding: .25rem;
            width: 100px;
            height: 100px;
            border: 1px solid rgb(255, 255, 255);
            border-radius: 54.0rem;
            background-color: #f8f9fa;
        }
    </style>
    <body>

        <div class="container mt-5">
            <h1 class="mb-4"><?php echo htmlspecialchars($utilisateur['nom_uti']) . ' ' . htmlspecialchars($utilisateur['prenom_uti']); ?></h1>
            <img src="<?php echo htmlspecialchars($utilisateur['image_uti']); ?>" alt="Avatar" class="img-thumbnail mb-4" style="width: 150px; height: 150px;">

            <h3>Informations personnelles :</h3>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur['mail_uti']); ?></p>
            <p><strong>Sexe :</strong> <?php echo htmlspecialchars($utilisateur['sexe_uti']); ?></p>
            <p><strong>Date d'inscription :</strong> <?php echo htmlspecialchars($utilisateur['date_inscription']); ?></p>

            <h3>Changer d'avatar :</h3>
            <form action="/ligue1/update_avatar" method="POST">
                <div class="mb-3">
                    <label for="avatar" class="form-label">Choisissez un nouvel avatar :</label>
                    <select name="avatar" id="avatar" class="form-select" required>
                        <option value="" disabled selected>Choisir un avatar</option>
                        <?php
                        // Lister les fichiers d'avatars disponibles
                        $avatars = glob("avatars/*.png"); // Assurez-vous que vos fichiers sont au format PNG
                        foreach ($avatars as $avatar) {
                            $filename = basename($avatar);
                            echo "<option value='avatars/$filename'>$filename</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="avatar-preview" class="mb-3">
                    <p><strong>Aperçu de l'avatar :</strong></p>
                    <img id="preview-image" src="" alt="Aperçu de l'avatar" style="display:none; width: 100px; height: 100px; border: 1px solid #ccc;">
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour l'avatar</button>
            </form>

            <!-- Section pour supprimer le compte -->
            <form action="/ligue1/delete_profil" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                <button type="submit" class="btn btn-danger mt-3">Supprimer mon compte</button>
            </form>


            <a href="/ligue1/" class="btn btn-secondary mt-4">Retour à l'accueil</a>
        </div>
        <script>
            document.getElementById('avatar').addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const avatarPath = selectedOption.value;
                const previewImage = document.getElementById('preview-image');

                // Afficher l'image de l'avatar sélectionné
                if (avatarPath) {
                    previewImage.src = avatarPath; // Met à jour le chemin de l'image
                    previewImage.style.display = 'block'; // Affiche l'image
                } else {
                    previewImage.style.display = 'none'; // Masque l'image si aucune option n'est sélectionnée
                }
            });
        </script>
    </body>
</html>