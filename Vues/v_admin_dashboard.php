<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Tableau de Bord Admin";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="/style/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <h1>Bienvenue sur le tableau de bord admin</h1>
            <p>Ceci est une page cachée, accessible uniquement aux administrateurs.</p>

            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <h2>Liste des utilisateurs</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['id_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['mail_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['date_inscription']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>