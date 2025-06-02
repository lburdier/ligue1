<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Classement Ligue 1</h2>
        <?php if (!empty($classement)): ?>
            <table class="table table-striped table-hover table-classement">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Club</th>
                        <th>Points</th>
                        <th>Victoires</th>
                        <th>Défaites</th>
                        <th>Matchs Nuls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classement as $team): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($team['position'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($team['nom_club'] ?? 'Inconnu'); ?></td>
                            <td><?php echo htmlspecialchars($team['points'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($team['victoires'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($team['defaites'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($team['matchs_nuls'] ?? '0'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Impossible de récupérer le classement pour le moment.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
