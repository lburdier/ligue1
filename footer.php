<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Footer - Ligue1</title>
        <style>
            html, body {
                height: 100%; /* Assurez-vous que le corps occupe 100% de la hauteur de la page */
                margin: 0; /* Supprimez les marges par défaut */
            }

            .navbar-nav {
                align-items: center;
            }
            .navbar-text {
                margin-right: 10px;
            }
            .navbar img {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                vertical-align: middle;
                margin-left: 5px;
            }
            .btn-deconnexion {
                margin-left: 15px;
                padding: 5px 15px;
            }
            footer {
                background-color: #f8f9fa; /* Light theme background */
                color: #333; /* Light theme text color */
                padding: 20px; /* Padding */
                text-align: center; /* Centrer le texte */
                position: relative; /* Pour le positionnement */
                bottom: 0; /* Reste en bas */
                width: 100%; /* Prend toute la largeur */
                border-top: 1px solid #ddd;
            }

            footer a {
                color: #007bff;
                text-decoration: none;
                margin: 0 10px;
            }

            footer a:hover {
                text-decoration: underline;
            }

            /* Dark theme styles */
            body.dark-mode footer {
                background-color: #1e1e1e; /* Dark theme background */
                color: #e0e0e0; /* Dark theme text color */
                border-top: 1px solid #444;
            }

            body.dark-mode footer a {
                color: #4fa3ff;
            }

            body.dark-mode footer a:hover {
                color: #82bfff;
            }
        </style>
    </head>
    <body>
        <footer>
            <div class="container text-center mt-4">
                <p>&copy; <?php echo date("Y"); ?> Ligue1. Tous droits réservés.</p>
                <p>
                    <a href="/ligue1/conditions">Conditions d'utilisation</a> | 
                    <a href="/ligue1/contact">Contact</a> | 
                    <a href="/ligue1/live_matches">Matchs en Direct</a>
                </p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9A4TCRmEkQAoO6mgD43W0lYtXyWgF6r3hAaH5CzKf4G0P5ht3z9" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyni8L6eG8FJ24z4ErBBFjyK4Y5ZsmZyPOnIg3b95IDB2KZ5Em6hdF" crossorigin="anonymous"></script>
    </body>
</html>