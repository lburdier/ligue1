<?php

/**
 * Description of Router
 *
 * @author perso
 */
class Router {

    private array $routes;
    private string $prefix;

    public function __construct() {
        $this->routes = []; // Création d'un tableau de routes
        $this->prefix = '/ligue1'; // Préfixe à ajouter aux routes
    }

    // Ajoute une route au routeur
    public function addRoute($url, $controllerFile) {
        $this->routes[$this->prefix . $url] = $controllerFile; // Ajouter le préfixe à l'URL
    }

    // Traite la demande actuelle
    public function execute($url) {
        $parsedUrl = strtok($url, '?'); // Supprimer les paramètres GET pour correspondre uniquement au chemin
        if (array_key_exists($parsedUrl, $this->routes)) {
            // Si l'URL correspond à une route, incluez le fichier du contrôleur
            $controllerFile = $this->routes[$parsedUrl];
            if (file_exists($controllerFile)) {
                include_once($controllerFile);
                exit; // Stop further execution after including the controller
            } else {
                // Gérer les erreurs si le fichier du contrôleur n'existe pas
                http_response_code(500);
                echo "Erreur : Contrôleur non trouvé pour l'URL : $parsedUrl";
                exit;
            }
        } else {
            // Gérer les erreurs 404 si l'URL n'est pas trouvée
            http_response_code(404);
            echo "<h1>Page non trouvée (Erreur 404)</h1>";
            echo "<p>URL demandée : " . htmlspecialchars($parsedUrl) . "</p>";
            exit;
        }
    }
}

// Initialiser le routeur
$router = new Router();

// Ajouter les routes
$router->addRoute('/', __DIR__ . '/Controleur/c_accueil.php');
$router->addRoute('/inscription', __DIR__ . '/Controleur/c_inscription.php');
$router->addRoute('/clubs', __DIR__ . '/Controleur/c_clubs.php');
$router->addRoute('/confirmation', __DIR__ . '/Controleur/c_confirmation.php');
$router->addRoute('/connexion', __DIR__ . '/Controleur/c_connexion.php');
$router->addRoute('/profil', __DIR__ . '/Controleur/c_profil.php');
$router->addRoute('/voir_article', __DIR__ . '/Controleur/c_voir_article.php');
$router->addRoute('/modifier_article', __DIR__ . '/Controleur/c_modifier_article.php');
$router->addRoute('/create_commentaire', __DIR__ . '/Controleur/c_create_commentaire.php');
$router->addRoute('/supprimer_commentaire', __DIR__ . '/Controleur/c_supprimer_commentaire.php');
$router->addRoute('/admin_dashboard', __DIR__ . '/Controleur/c_admin_dashboard.php');
$router->addRoute('/admin_m1', __DIR__ . '/Controleur/c_admin_dashboard_v2.php'); // Updated route for admin dashboard v2
$router->addRoute('/article', __DIR__ . '/Controleur/c_article.php');
$router->addRoute('/create_article', __DIR__ . '/Controleur/c_create_article.php');
$router->addRoute('/analyse_logs', __DIR__ . '/Controleur/c_analyse_logs.php');
$router->addRoute('/generate_image', __DIR__ . '/Vues/v_generate_image.php');
$router->addRoute('/classement', __DIR__ . '/Controleur/c_classement.php');
$router->addRoute('/statistiques_joueurs', __DIR__ . '/Vues/v_statistiques_joueurs.php');
$router->addRoute('/update_actualite', __DIR__ . '/Controleur/c_actualite_auto.php');
$router->addRoute('/generate_report', __DIR__ . '/Controleur/c_generate_report.php');
$router->addRoute('/live_matches', __DIR__ . '/Controleur/c_live_matches.php');
$router->addRoute('/club_details', __DIR__ . '/Controleur/c_club_details.php');
$router->addRoute('/suivre_club', __DIR__ . '/Controleur/c_suivre_club.php');
$router->addRoute('/actualites', __DIR__ . '/Controleur/c_actualites.php');

// Exécuter le routeur avec l'URL actuelle
$router->execute($_SERVER['REQUEST_URI']);
?>
