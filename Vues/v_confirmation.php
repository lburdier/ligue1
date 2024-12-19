<?php
ob_start();

// Vérifiez si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

$nom = $_SESSION['nom'] ?? 'Utilisateur'; // Utiliser "Utilisateur" par défaut si le nom n'est pas trouvé
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Ligue1</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">Inscription réussie</h1>
        <p>Merci pour votre inscription, <strong><?php echo htmlspecialchars($nom); ?></strong>!</p>
        <p>Vous pouvez maintenant vous connecter avec vos identifiants.</p>
        <a href="/ligue1/connexion" class="btn btn-primary mb-4">Se connecter</a>
    </div>

</body>
</html>
<?php
ob_end_flush(); // Finaliser le tampon et envoyer la sortie
?>