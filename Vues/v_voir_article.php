<?php
// Ensure variables are defined to avoid undefined variable errors
$article = $article ?? null;
$commentaires = $commentaires ?? [];
$proprietaire = $proprietaire ?? ['prenom_uti' => '', 'nom_uti' => ''];
$estProprietaire = $estProprietaire ?? false;

// Vérifier si l'article est défini
if (!isset($article)) {
    echo "<p>Erreur : L'article demandé est introuvable.</p>";
    exit;
}

// Vérifier si l'utilisateur a les droits pour commenter
$userData = $_SESSION['user'] ?? [];
$userData['role'] = $userData['role'] ?? 'user'; // Ensure 'role' is always defined
$canComment = in_array($userData['role'], ['createur', 'commentateur']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/ligue1/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

    </style>
</head>

<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>

    <div class="article-container">
        <div class="article-header">
            <h1><?php echo htmlspecialchars($article->getTitre(), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="category"><strong>Catégorie :</strong> <?php echo htmlspecialchars($article->getCategorie(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Publié le :</strong> <?php echo htmlspecialchars($article->getDateCreation(), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <img src="<?php echo htmlspecialchars($article->getImage(), ENT_QUOTES, 'UTF-8'); ?>" alt="Image de l'article" class="article-image">

        <div class="article-content">
            <p><?php echo nl2br(htmlspecialchars($article->getContenu(), ENT_QUOTES, 'UTF-8')); ?></p>
        </div>

        <div class="comment-section">
            <h3>Commentaires</h3>
            <?php if (!empty($commentaires)): ?>
                <?php foreach ($commentaires as $commentaire): ?>
                    <div class="comment">
                        <strong><?php echo htmlspecialchars($commentaire['auteur'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <em><?php echo htmlspecialchars($commentaire['date_commentaire'], ENT_QUOTES, 'UTF-8'); ?></em>
                        <p><?php echo nl2br(htmlspecialchars($commentaire['contenu'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <?php if ($userData['role'] === 'gestionnaire de commentaires' || $userData['role'] === 'superviseur'): ?>
                            <a href="/ligue1/supprimer_commentaire?id=<?php echo urlencode($commentaire['id_commentaire'] ?? ''); ?>" class="btn btn-danger btn-sm">Supprimer</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun commentaire pour cet article.</p>
            <?php endif; ?>

            <?php if ($canComment): ?>
                <form method="post" action="/ligue1/c_create_commentaire" class="comment-form">
                    <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($article->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                    <textarea name="contenu" required minlength="5" maxlength="255" placeholder="Ajoutez un commentaire..."></textarea>
                    <button type="submit">Commenter</button>
                </form>
            <?php else: ?>
                <p>Vous n'avez pas les droits pour ajouter des commentaires.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
