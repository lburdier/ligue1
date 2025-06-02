<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Joueurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Statistiques des Joueurs</h2>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Club</th>
                    <th>Buts</th>
                    <th>Passes Décisives</th>
                    <th>Cartons Jaunes</th>
                    <th>Cartons Rouges</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($joueurs as $index => $joueur): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['club']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['buts']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['passes']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['jaunes']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['rouges']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
