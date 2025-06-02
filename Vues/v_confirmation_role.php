<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Devenir Rédacteur</title>
    <link rel="stylesheet" href="/ligue1/style/style.css?v=<?= time(); ?>"> <!-- Use the correct CSS file -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRqE2qN5rj6vb2Tz5l5aU3HTTxGGOGHX5pTXyjVXA" crossorigin="anonymous">
</head>
<body>
    <?php include '../menu.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Confirmation</h1>
        <p>Êtes-vous sûr de vouloir devenir rédacteur d'articles ?</p>
        <form action="/ligue1/Controleur/c_demande_confirmation.php" method="POST">
            <input type="hidden" name="role_createur" value="<?php echo htmlspecialchars($_SESSION['demande_role'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-primary">Confirmer</button>
            <a href="/ligue1/Vues/v_profil.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>
</html>
