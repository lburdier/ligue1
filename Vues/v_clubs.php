<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Ligue1'; ?></title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <!-- Affichage des clubs -->
    <div class="container mt-5">
        <h1 class="mb-4">Liste des clubs</h1>
        <div class="row">
            <?php if (!empty($tab)): ?>
                <?php foreach ($tab as $club): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($club->getNom()); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($club->getEmplacement()); ?></h6>
                                <p class="card-text"><?php echo htmlspecialchars($club->getLigue()); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        Aucun club trouvé.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
