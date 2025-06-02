<?php 
// Start output buffering to prevent premature output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Include the GestionBDD class
include_once __DIR__ . '/Models/GestionBDD.php';
include_once __DIR__ . '/Models/GestionUtilisateur.php';

// Ensure $_SESSION['user'] is defined to avoid undefined index errors
$_SESSION['user'] = $_SESSION['user'] ?? [];

// Initialiser les variables pour l'utilisateur connecté
$utilisateurConnecte = isset($_SESSION['user']) && !empty($_SESSION['user']);
$nomUtilisateur = $utilisateurConnecte ? $_SESSION['user']['nom'] : 'Invité';
$imageProfil = $utilisateurConnecte ? $_SESSION['user']['image'] : 'avatars/default_avatar.png';

$gestionBDD = new GestionBDD("BD_ligue1");
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

$clubsSuivis = [];
if ($utilisateurConnecte) {
    $clubsSuivis = $gestionUtilisateur->getClubsSuivis($_SESSION['user']['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Ligue1</title>
    <link rel="stylesheet" href="/ligue1/style/style.css?v=<?= time(); ?>"> <!-- Use the correct CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky; /* Make the header sticky */
            top: 0; /* Stick to the top of the viewport */
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95); /* Slightly opaque background */
            backdrop-filter: blur(8px); /* Add a blur effect for better visibility */
            padding: 10px 20px;
            z-index: 1000; /* Ensure it stays above other elements */
            border-bottom: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add a subtle shadow for depth */
        }

        .navbar-container {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .navbar.dark-mode {
            display: flex;
            justify-content: space-between;
            position: sticky; /* Make the header sticky */
            top: 0; /* Stick to the top of the viewport */
            width: 100%;
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
            border-bottom: 2px solid #333 !important;            
            backdrop-filter: blur(8px); /* Add a blur effect for better visibility */
            padding: 10px 20px;
            z-index: 1000; /* Ensure it stays above other elements */
            border-bottom: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add a subtle shadow for depth */
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            margin-right: 20px;
            color: #1f3c88;
        }

        .nav-menu ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-menu li {
            margin: 0 10px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #ffffff;
            font-weight: 500;
        }

        .nav-menu a:hover {
            color:rgb(48, 107, 255);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .avatar {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin: 0 10px;
        }

        .btn-deconnexion, .btn-connexion {
            margin-left: 10px;
            color: #0d6efd;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-deconnexion:hover, .btn-connexion:hover {
            text-decoration: underline;
        }

        .navbar-text a, .navbar-text a:focus, .navbar-text a:hover {
            color: rgb(0 154 255)!important;
        }

        /* Chatbot dialog styles with min and max resize constraints */
        #chatbot-dialog {
            resize: both; /* Allow resizing */
            overflow: auto; /* Ensure content is scrollable when resized */
            position: absolute; /* Allow dragging */
            cursor: grab; /* Indicate draggable area */
            max-width: 500px; /* Maximum width */
            max-height: 300px; /* Maximum height */
            min-width: 300px; /* Minimum width */
            min-height: 200px; /* Minimum height */
        }

        #chatbot-dialog.dragging {
            cursor: grabbing; /* Change cursor when dragging */
        }

        /* Chatbot expanded mode styles */
        #chatbot-dialog.expanded {
            width: 80%; /* Expand to 80% of the screen width */
            height: 80%; /* Expand to 80% of the screen height */
            top: 10%; /* Center vertically */
            left: 10%; /* Center horizontally */
            right: auto;
            bottom: auto;
            position: fixed;
        }

        #chatbot-expand {
            margin-left: 10px;
            padding: 8px 12px;
            background-color: #525252a6;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        #chatbot-expand:hover {
            background-color: rgb(198, 198, 198);
        }
    </style>
</head>
<body>
    <!-- Barre de navigation custom -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="/ligue1" class="navbar-brand">Ligue1</a>
            <nav class="nav-menu">
                <ul>
                    <li><a href="/ligue1">Accueil</a></li>
                    <li><a href="/ligue1/inscription">Inscription</a></li>
                    <li><a href="/ligue1/clubs">Clubs</a></li>
                    <li><a href="/ligue1/classement">Classement</a></li>
                    <li><a href="/ligue1/article">Article</a></li>
                    <?php if ($utilisateurConnecte): ?>
                        <li><a href="/ligue1/create_article">Créer un article</a></li>
                        <?php if (!empty($clubsSuivis)): ?>
                            <li><a href="/ligue1/actualites">Actualités</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li><a href="/ligue1/profil">Mon Profil</a></li>
                </ul>
            </nav>
            <div class="navbar-actions">
                <div class="toggle-container">
                    <input type="checkbox" class="checkbox" id="checkbox">
                    <label for="checkbox" class="checkbox-label">
                        <i class="fas fa-moon"></i>
                        <i class="fas fa-sun"></i>
                        <span class="ball"></span>
                    </label>
                </div>
                <div class="user-actions">
                    <?php if ($utilisateurConnecte): ?>
                        <span class="navbar-text">
                            Bonjour, <a href="/ligue1/profil"><?php echo htmlspecialchars($nomUtilisateur); ?></a>
                        </span>
                        <a href="/ligue1/profil">
                            <img src="<?php echo htmlspecialchars($imageProfil); ?>" alt="Avatar" class="avatar">
                        </a>
                        <a href="/ligue1/logout.php" class="btn-deconnexion">Se déconnecter</a>
                    <?php else: ?>
                        <a href="/ligue1/connexion" class="btn-connexion">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('checkbox');

            // Set dark mode as the default
            document.body.classList.add('dark-mode');
            const elementsToToggle = document.querySelectorAll(
                '.article-user, .rc-anchor-light, .recaptcha-container .rc-anchor, .form-select, .article-item, .commentaire, .navbar, .card, .custom-jumbotron, h1, h2, h3, p, .btn-details, .btn, form, footer'
            );
            elementsToToggle.forEach(el => el.classList.add('dark-mode'));
            checkbox.checked = true;

            // Allow toggling between light and dark mode
            checkbox.addEventListener('change', function () {
                const isDark = this.checked;
                document.body.classList.toggle('dark-mode', isDark);
                elementsToToggle.forEach(el => el.classList.toggle('dark-mode', isDark));
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

            const chatbotExpand = document.createElement('button');
            chatbotExpand.id = 'chatbot-expand';
            chatbotExpand.textContent = 'Agrandir';
            document.getElementById('chatbot-input-container').appendChild(chatbotExpand);

            chatbotExpand.addEventListener('click', () => {
                chatbotDialog.classList.toggle('expanded');
                if (chatbotDialog.classList.contains('expanded')) {
                    chatbotExpand.textContent = 'Réduire';
                } else {
                    chatbotExpand.textContent = 'Agrandir';
                    // Reset position to default
                    chatbotDialog.style.top = '';
                    chatbotDialog.style.left = '';
                    chatbotDialog.style.bottom = '90px';
                    chatbotDialog.style.right = '20px';
                }
            });

            // Make chatbot draggable
            let isDragging = false;
            let offsetX, offsetY;

            chatbotDialog.addEventListener('mousedown', (e) => {
                if (e.target.id === 'chatbot-header') {
                    isDragging = true;
                    offsetX = e.clientX - chatbotDialog.offsetLeft;
                    offsetY = e.clientY - chatbotDialog.offsetTop;
                    chatbotDialog.classList.add('dragging');
                }
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    chatbotDialog.style.left = `${e.clientX - offsetX}px`;
                    chatbotDialog.style.top = `${e.clientY - offsetY}px`;
                }
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    chatbotDialog.classList.remove('dragging');
                }
            });
        });
    </script>
</body>
</html>