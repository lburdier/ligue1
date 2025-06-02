<?php
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}

// Définir le titre par défaut
$pageTitles = [
    '/'                 => 'Accueil',
    '/ligue1/'          => 'Accueil',
    '/inscription'      => 'Inscription',
    '/clubs'            => 'Clubs',
    '/confirmation'     => 'Confirmation',
    '/connexion'        => 'Connexion',
    '/delete_profil'    => 'Suppression de Compte',
    '/article'          => 'Article',
    '/modifier_article' => 'Modifier l\'article',
    '/create_article'   => 'Créer un article',
];

// Récupérer l'URL actuelle et définir le titre de la page
$url       = strtok($_SERVER['REQUEST_URI'], '?'); // Supprimer les paramètres GET
$pageTitle = isset($pageTitles[$url]) ? $pageTitles[$url] : 'Page Non Trouvée';

// Redirection vers la page d'accueil si l'utilisateur accède directement à index.php
if ($url === '/ligue1/index.php') {
    header('Location: /ligue1/');
    exit();
}

// Inclure le menu depuis le bon chemin
include_once __DIR__ . '/menu.php';

// Inclure le fichier du routeur
require_once __DIR__ . '/Router.php';

include_once __DIR__ . '/Models/GestionBDD.php';

try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Vérifier et créer la table actualite si nécessaire
    $gestionBDD->createActualiteTableIfNotExists();

    // Liste des tables nécessaires
    $requiredTables = [
        'arbitre', 'article', 'banni_ips', 'championnat', 'club', 
        'commentaire', 'logs', 'news', 'project', 'rencontre', 
        's_abonner', 'saison', 'stade', 'utilisateur'
    ];

    // Vérifier l'existence des tables
    $missingTables = $gestionBDD->checkTablesExist($requiredTables);
    if (!empty($missingTables)) {
        echo "<div style='
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #dc3545;
            border-radius: 8px;
            background-color: #f8d7da;
            color: #721c24;
            font-family: Arial, sans-serif;
            text-align: center;
        '>
            <h2>🚫 Erreur</h2>
            <p>Les tables suivantes sont manquantes dans la base de données :</p>
            <ul>";
        foreach ($missingTables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>
            <p>Veuillez vérifier votre base de données.</p>
        </div>";
        exit;
    }
} catch (Exception $e) {
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Créer une instance de routeur et définir les routes
$router = new Router();
$router->addRoute('/', __DIR__ . '/Controleur/c_accueil.php');
$router->addRoute('/ligue1/', __DIR__ . '/Controleur/c_accueil.php');
$router->addRoute('/inscription', __DIR__ . '/Controleur/c_inscription.php');
$router->addRoute('/clubs', __DIR__ . '/Controleur/c_clubs.php');
$router->addRoute('/confirmation', __DIR__ . '/Controleur/c_confirmation.php');
$router->addRoute('/connexion', __DIR__ . '/Controleur/c_connexion.php');
$router->addRoute('/profil', __DIR__ . '/Controleur/c_profil.php');
$router->addRoute('/admin_m1', __DIR__ . '/Controleur/c_admin_dashboard.php');
$router->addRoute('/admin_m2', __DIR__ . '/Controleur/c_admin_dashboard_v2.php');
$router->addRoute('/article', __DIR__ . '/Controleur/c_article.php');
$router->addRoute('/create_article', __DIR__ . '/Controleur/c_create_article.php');

// Gestion de routes spécifiques avec des inclusions directes
if ($url === '/ligue1/c_create_article.php') {
    // Supprimer ce bloc car il est redondant
    // include __DIR__ . '/Controleur/c_create_article.php';
    // exit;
}

if ($url === '/ligue1/c_update_article.php') {
    include __DIR__ . '/Controleur/c_update_article.php';
    exit;
}

if (preg_match('/\/ligue1\/modifier_article\?id=\d+/', $url)) {
    include_once __DIR__ . '/Controleur/c_modifier_article.php';
    exit;
}

if ($url === '/ligue1/update_avatar') {
    include __DIR__ . '/Controleur/c_update_avatar.php';
    exit;
}

if (preg_match('/\/ligue1\/voir_article\?id=\d+/', $_SERVER['REQUEST_URI'])) {
    include_once __DIR__ . '/Controleur/c_voir_article.php';
    exit;
}

if ($url === '/ligue1/delete_profil') {
    include __DIR__ . '/Controleur/c_delete_profil.php';
    exit;
}

// Remove this block as it duplicates the router's logic
// if ($url === '/ligue1/create_article') {
//     include __DIR__ . '/Controleur/c_create_article.php';
//     exit;
// }

if ($url === '/check_email') {
    include __DIR__ . '/check_email.php';
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
        <title>Ligue1 -                                                                                                                                                                                                                                       <?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="/ligue1/style/style.css?v=<?= time(); ?>"> <!-- Use the correct CSS file -->
        <style>
            /* Chatbot button styles */
            #chatbot-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                background-color: #ffffff;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                z-index: 1000;
            }
            #chatbot-button img {
                width: 30px;
                height: 30px;
            }

            /* Chatbot dialog styles */
            #chatbot-dialog {
                backdrop-filter: blur(8px);
                position: fixed;
                bottom: 90px;
                right: 20px;
                width: 300px;
                max-height: 400px;
                background-color: #2a2a2a85;
                border: 1px solid #585858ad;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                display: none;
                flex-direction: column;
                z-index: 1000;
                resize: both; /* Allow resizing */
                overflow: auto; /* Ensure content is scrollable when resized */
            }

            #chatbot-header {
                background-color: #6d6d6d57;
                color: #fff;
                padding: 10px;
                border-radius: 8px 8px 0 0;
                font-weight: bold;
                text-align: center;
            }

            #chatbot-messages {
                flex: 1;
                padding: 10px;
                overflow-y: auto;
                font-size: 0.9rem;
            }

            #chatbot-input-container {
                display: flex;
                padding: 10px;
                border-top: 1px solid #3d3d3d;
            }

            #chatbot-input {
                color: #fff;
                flex: 1;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 0.9rem;
            }

            #chatbot-send {
                margin-left: 10px;
                padding: 8px 12px;
                background-color: #525252a6;
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 0.9rem;
            }

            #chatbot-send:hover {
                background-color:rgb(198, 198, 198);
            }

            .chatbot-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 50px;
                height: 50px;
                background-color: #1f3c88;
                color: white;
                border-radius: 50%;
                cursor: pointer;
                font-size: 24px;
                transition: background-color 0.3s ease;
            }

            .chatbot-btn:hover {
                background-color: #1554b4;
            }
        </style>
    </head>
    <body>
        <footer>
            <?php include './footer.php'; ?>
        </footer>

        <!-- Chatbot button -->
        <div id="chatbot-button" class="chatbot-btn">
            🚀
        </div>

        <!-- Chatbot dialog -->
        <div id="chatbot-dialog">
            <div id="chatbot-header">Chatbot</div>
            <div id="chatbot-messages"></div>
            <div id="chatbot-input-container">
                <input type="text" id="chatbot-input" placeholder="Posez une question...">
                <button id="chatbot-send">Envoyer</button>
            </div>
        </div>

        <script>
            const chatbotButton = document.getElementById('chatbot-button');
            const chatbotDialog = document.getElementById('chatbot-dialog');
            const chatbotMessages = document.getElementById('chatbot-messages');
            const chatbotInput = document.getElementById('chatbot-input');
            const chatbotSend = document.getElementById('chatbot-send');

            // Toggle chatbot dialog visibility
            chatbotButton.addEventListener('click', () => {
                chatbotDialog.style.display = chatbotDialog.style.display === 'flex' ? 'none' : 'flex';
            });

            // Send a message to the chatbot
            chatbotSend.addEventListener('click', async () => {
                const userMessage = chatbotInput.value.trim();
                if (!userMessage) return;

                // Display user message
                const userMessageElement = document.createElement('div');
                userMessageElement.textContent = `Vous: ${userMessage}`;
                userMessageElement.style.marginBottom = '10px';
                chatbotMessages.appendChild(userMessageElement);

                // Clear input
                chatbotInput.value = '';

                // Call Mistral AI API
                const responseMessageElement = document.createElement('div');
                responseMessageElement.textContent = 'Chatbot: En cours de réponse...';
                responseMessageElement.style.marginBottom = '10px';
                chatbotMessages.appendChild(responseMessageElement);

                try {
                    const response = await fetch('/ligue1/api/chatbot.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: userMessage })
                    });

                    const data = await response.json();
                    responseMessageElement.textContent = `Chatbot: ${data.response}`;
                } catch (error) {
                    responseMessageElement.textContent = 'Chatbot: Une erreur est survenue.';
                }

                // Scroll to the bottom of the messages
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            });

            // Ensure chatbot dialog remains within the viewport when resized
            chatbotDialog.addEventListener('resize', () => {
                const rect = chatbotDialog.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    chatbotDialog.style.left = `${window.innerWidth - rect.width}px`;
                }
                if (rect.bottom > window.innerHeight) {
                    chatbotDialog.style.top = `${window.innerHeight - rect.height}px`;
                }
            });
        </script>
    </body>
</html>