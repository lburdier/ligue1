<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="style/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    </head>
    <body>

        <div class="container mt-5">
            <h1>Êtes-vous sûr de vouloir supprimer votre compte ?</h1>
            <p>Cette action est irréversible et toutes vos informations seront perdues.</p>

            <form action="/ligue1/delete_profil" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
            </form>

            <a href="/ligue1/profil" class="btn btn-secondary">Retour au profil</a>
        </div>

    </body>
</html>