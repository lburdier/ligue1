<?php
// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo htmlspecialchars($article->getContenu(), ENT_QUOTES, 'UTF-8'); ?></p>

        <h2>Commentaires</h2>
        <?php if (!empty($commentaires)): ?>
            <?php foreach ($commentaires as $commentaire) : ?>
                <div class="comment">
                    <strong><?php echo htmlspecialchars($commentaire->getAuteur(), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <p><?php echo htmlspecialchars($commentaire->getContenu(), ENT_QUOTES, 'UTF-8'); ?></p>
                    <small><?php echo htmlspecialchars($commentaire->getDateCommentaire(), ENT_QUOTES, 'UTF-8'); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-comment">Aucun commentaire pour cet article.</p>
        <?php endif; ?>

        <?php if ($isLoggedIn) : ?>
            <form action="/ligue1/ajouter_commentaire.php" method="post">
                <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($article->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="contenu">Ajouter un commentaire</label>
                    <textarea class="form-control" id="contenu" name="contenu" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        <?php else : ?>
            <div class="alert alert-info">
                Veuillez vous inscrire pour ajouter des commentaires.
            </div>
        <?php endif; ?>
        <?php if (!$isLoggedIn || $_SESSION['user']['role'] !== 'rédacteur de commentaires') : ?>
            <div class="alert alert-warning">
                Vous n'avez pas les droits pour ajouter des commentaires.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
