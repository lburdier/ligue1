<?php
// Définir le titre par défaut
$pageTitles = [
    '/' => 'Accueil',
    '/ligue1/' => 'Accueil',
    '/inscription' => 'Inscription',
    '/clubs' => 'Clubs',
    '/confirmation' => 'Confirmation',
    '/connexion' => 'Connexion',
    '/delete_profil' => 'Suppression de Compte',
    '/article' => 'Article',
    '/modifier_article' => 'Modifier l\'article', // Titre pour la modification d'article
    '/portfolio' => 'Portfolio',
    '/create_article' => 'Créer un article' // Titre pour la création d'un article
];

// Récupérer l'URL actuelle et définir le titre de la page
$url = $_SERVER['REQUEST_URI'];
$pageTitle = isset($pageTitles[$url]) ? $pageTitles[$url] : 'Page Non Trouvée';

// Redirection vers la page d'accueil si l'utilisateur accède directement à index.php
if ($url === '/ligue1/index.php' || $url === '/ligue1') {
    header('Location: /ligue1/');
    exit();
}

// Inclure le fichier du routeur et du menu
include_once './Router.php';
include './menu.php';

// Créer une instance de routeur et définir les routes
$router = new Router();
$router->addRoute('/', './Controleur/c_accueil.php');
$router->addRoute('/ligue1/', './Controleur/c_accueil.php');
$router->addRoute('/inscription', './Controleur/c_inscription.php');
$router->addRoute('/clubs', './Controleur/c_clubs.php');
$router->addRoute('/confirmation', './Controleur/c_confirmation.php');
$router->addRoute('/connexion', './Controleur/c_connexion.php');
$router->addRoute('/profil', './Controleur/c_profil.php');
$router->addRoute('/admin_m1', './Controleur/c_admin_dashboard.php');
$router->addRoute('/admin_m2', './Controleur/c_admin_dashboard_v2.php');
$router->addRoute('/article', './Controleur/c_article.php');
$router->addRoute('/portfolio', './Controleur/c_portfolio.php'); // Nouvelle route pour le portfolio
$router->addRoute('/create_article', './Controleur/c_create_article.php'); // Nouvelle route pour la création d'un article
// Gestion de la route pour la création d'un article
if ($url === '/ligue1/c_create_article.php') {
    include __DIR__ . '/Controleur/c_create_article.php';
    exit;
}

// Gestion de la route pour la mise à jour d'un article
if ($url === '/ligue1/c_update_article.php') {
    include __DIR__ . '/Controleur/c_update_article.php';
    exit;
}

// Gestion de routes spécifiques avec des inclusions directes
if (preg_match('/\/ligue1\/modifier_article\?id=\d+/', $url)) {
    include_once __DIR__ . '/Controleur/c_modifier_article.php';
    exit;
}

// Gestion de routes spécifiques avec des inclusions directes
if ($url === '/ligue1/update_avatar') {
    include __DIR__ . '/Controleur/c_update_avatar.php';
    exit;
}

if ($url === '/ligue1/c_create_commentaire.php') {
    include_once __DIR__ . '/Controleur/c_create_commentaire.php';
    exit;
}

if (preg_match('/\/ligue1\/voir_article\?id=\d+/', $url)) {
    include_once __DIR__ . '/Controleur/c_voir_article.php';
    exit;
}

if ($url === '/ligue1/delete_profil') {
    include __DIR__ . '/Controleur/c_delete_profil.php';
    exit;
}

if ($url === '/check_email') {
    include '/check_email.php';
    exit();
}

// Traiter la demande de l'URL actuelle
$router->execute($url);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ligue1 - <?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="/style/style.css">
    </head>
    <body>
        <footer>
            <?php include './footer.php'; ?>
        </footer>
    </body>
</html>