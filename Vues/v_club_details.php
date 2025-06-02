<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Club</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Détails du Club</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($clubDetails['nom_club'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="card-text"><strong>Ligue :</strong> <?php echo htmlspecialchars($clubDetails['ligue_club'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($clubDetails['nom_stade'])): ?>
                    <p class="card-text"><strong>Stade :</strong> <?php echo htmlspecialchars($clubDetails['nom_stade'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="card-text"><strong>Ville :</strong> <?php echo htmlspecialchars($clubDetails['ville'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="card-text"><strong>Capacité :</strong> <?php echo number_format($clubDetails['capacite'], 0, ',', ' '); ?></p>
                    <p class="card-text"><strong>Adresse :</strong> <?php echo htmlspecialchars($clubDetails['adresse'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="mt-4">
                        <h5>Localisation :</h5>
                        <iframe 
                            width="100%" 
                            height="300" 
                            frameborder="0" 
                            style="border:0" 
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDRrzgs7oIlxk1Zi8gc_aHMMFJilijUdgU&q=<?php echo urlencode($clubDetails['adresse']); ?>" 
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php else: ?>
                    <p class="card-text text-warning">Aucun stade attribué.</p>
                <?php endif; ?>
                <a href="/ligue1/clubs" class="btn btn-secondary mt-3">Retour à la liste des clubs</a>
            </div>
        </div>
    </div>
</body>
</html>
