<html>
    <head>
        <meta charset="UTF-8">
        <title>Ligue1</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
              integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRqE2qN5rj6vb2Tz5l5aU3HTTxGGOGHX5pTXyjVXA" 
              crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
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
                if (array_key_exists($url, $this->routes)) {
                    // Si l'URL correspond à une route, incluez le fichier du contrôleur
                    $controllerFile = $this->routes[$url];
                    if (file_exists($controllerFile)) {
                        include_once($controllerFile);
                    } else {
                        // Gérer les erreurs si le fichier du contrôleur n'existe pas
                        echo "Erreur : Contrôleur non trouvé";
                    }
                } else {
                    // Gérer les erreurs 404 si l'URL n'est pas trouvée
                    echo "Page non trouvée (Erreur 404)";
                }
            }
        }
        ?>
    </body>
</html>
