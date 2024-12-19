<?php
// Vérifier si la session est démarrée, sinon la démarrer
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables pour l'utilisateur connecté
$utilisateurConnecte = isset($_SESSION['user']) && !empty($_SESSION['user']);
$nomUtilisateur = $utilisateurConnecte ? $_SESSION['user']['nom'] : 'Invité';
$imageProfil = $utilisateurConnecte ? $_SESSION['user']['image'] : 'avatars/default_avatar.png';
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Menu - Ligue1</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="style/style.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/ligue1">Ligue1</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/ligue1">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ligue1/inscription">Inscription</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ligue1/clubs">Clubs</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ligue1/article">Article</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ligue1/create_article">Crée un article</a></li> <!-- Lien ajouté pour le portfolio -->
                    <li class="nav-item"><a class="nav-link" href="/ligue1/portfolio">portfolio</a></li> <!-- Lien ajouté pour le portfolio -->
                    <li class="nav-item"><a class="nav-link" href="/ligue1/profil">Mon Profil</a></li>
                </ul>
                <div class="toggle-container">
                    <input type="checkbox" class="checkbox" id="checkbox">
                    <label for="checkbox" class="checkbox-label">
                        <i class="fas fa-moon"></i>
                        <i class="fas fa-sun"></i>
                        <span class="ball"></span>
                    </label>
                </div>
                <div>
                    <?php if ($utilisateurConnecte): ?>
                        <span class="navbar-text me-2">
                            Bonjour, <a href="/ligue1/profil" style="text-decoration: none; color: inherit;"><?php echo htmlspecialchars($nomUtilisateur); ?></a>
                        </span>
                        <a href="/ligue1/profil">
                            <img src="<?php echo htmlspecialchars($imageProfil); ?>" alt="Avatar" class="rounded-circle" width="40">
                        </a>
                        <a href="/ligue1/logout.php" class="btn-deconnexion">Se déconnecter</a>
                    <?php else: ?>
                        <a class="nav-link" href="/ligue1/connexion">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('checkbox');

            // Vérifier l'état enregistré dans le localStorage et appliquer le mode sombre si nécessaire
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            document.body.classList.toggle('dark-mode', isDarkMode);
            checkbox.checked = isDarkMode;

            // Appliquer la classe 'dark-mode' à tous les éléments pertinents
            document.querySelectorAll('.article-user, .rc-anchor-light, .recaptcha-container .rc-anchor, .form-select, .article-item, .commentaire, .navbar, .card, .custom-jumbotron, h1, h2, h3, p, .btn-details, .btn, form, footer').forEach(el => {
                el.classList.toggle('dark-mode', isDarkMode);
            });

            // Écouter le changement d'état de la case à cocher
            checkbox.addEventListener('change', function () {
                const isDarkMode = this.checked;
                document.body.classList.toggle('dark-mode', isDarkMode);

                // Appliquer ou retirer la classe 'dark-mode' à tous les éléments pertinents
                document.querySelectorAll('.article-user, .rc-anchor-light, .recaptcha-container .rc-anchor, .form-select, .article-item, .commentaire, .navbar, .card, .custom-jumbotron, h1, h2, h3, p, .btn-details, .btn, form, footer').forEach(el => {
                    el.classList.toggle('dark-mode', isDarkMode);
                });

                // Enregistrer l'état du mode sombre dans le localStorage
                localStorage.setItem('darkMode', isDarkMode);
            });
        });
    </script>
</html>