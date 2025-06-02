<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stades des clubs - Ligue 1</title>
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h1>🏟️ Stades principaux des clubs de Ligue 1</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Club</th>
                    <th>Stade</th>
                    <th>Ville</th>
                    <th>Capacité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stades as $stade): ?>
                    <tr>
                        <td><?= htmlspecialchars($stade['nom_club']) ?></td>
                        <td><?= htmlspecialchars($stade['nom_stade']) ?></td>
                        <td><?= htmlspecialchars($stade['ville']) ?></td>
                        <td><?= number_format($stade['capacite'], 0, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
