<?php
$generalNews = $generalNews ?? [];
$actualites = $actualites ?? [];
$showGeneralNews = filter_input(INPUT_GET, 'show_general', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== false;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Actualités') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Actualités Générales</h2>
        <div class="mb-3">
            <a href="?show_general=<?= $showGeneralNews ? '0' : '1'; ?>" 
               class="btn btn-<?= $showGeneralNews ? 'danger' : 'success'; ?>">
               <?= $showGeneralNews ? 'Masquer les actualités générales' : 'Afficher les actualités générales'; ?>
            </a>
        </div>

        <?php if ($showGeneralNews): ?>
            <?php if (!empty($generalNews)): ?>
                <div class="row g-4">
                    <?php foreach ($generalNews as $news): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm">
                                <img src="<?= !empty($news['image']) ? htmlspecialchars($news['image']) : '/ligue1/img/placeholder.jpg'; ?>" 
                                     onerror="this.onerror=null; this.src='/ligue1/img/placeholder.jpg';"
                                     class="card-img-top" alt="Image de l'actualité" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($news['titre']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($news['contenu']); ?></p>
                                    <p class="card-text"><small class="text-muted">Publié le <?= htmlspecialchars($news['date_publication']); ?></small></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Aucune actualité générale disponible.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Actualités des Clubs Suivis</h2>
        <?php if (!empty($actualites)): ?>
            <div class="row g-4">
                <?php foreach ($actualites as $actu): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= !empty($actu['image']) ? htmlspecialchars($actu['image']) : '/ligue1/img/placeholder.jpg'; ?>" 
                                 onerror="this.onerror=null; this.src='/ligue1/img/placeholder.jpg';"
                                 class="card-img-top" alt="Image de l'actualité" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($actu['titre']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($actu['contenu']); ?></p>
                                <p class="card-text"><small class="text-muted">Publié le <?= htmlspecialchars($actu['date_publication']); ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Aucune actualité pour les clubs suivis.</p>
        <?php endif; ?>
    </div>
</body>
</html>
