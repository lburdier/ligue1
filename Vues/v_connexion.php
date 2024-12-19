<?php
ob_start();

$pageTitle = "Ligue1 - Connexion"; // Titre de la page de connexion
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRqE2qN5rj6vb2Tz5l5aU3HTTxGGOGHX5pTXyjVXA"
          crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .g-recaptcha {
            margin-bottom: 20px; /* Ajustez la valeur selon vos besoins */
        }
        input:valid {
            border-color: lightgreen;
            background-color: #e9ffe9;
        }
        input:invalid, input.email-error {
            border-color: red;
            background-color: #ffe9e9;
        }
        .error {
            color: red;
            margin-top: 5px;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid transparent;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Connexion</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo htmlspecialchars_decode($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="connexion-form" action="/ligue1/connexion" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="motdepasse" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
            </div>
            <!-- reCAPTCHA v2 widget -->
            <div class="g-recaptcha mb-3" data-sitekey="6Leg3HUqAAAAAP61ZsD0bRG6zDURmrYHqB_UOQ9k" data-theme="dark"></div>
            <button type="submit" class="btn btn-primary mb-4">Se connecter</button>
        </form>

        <p class="mt-3">Pas encore inscrit? <a href="/ligue1/inscription">Créer un compte</a></p>

        <div class="mt-4">
            <label for="test-users" class="form-label">Déboguer avec un utilisateur de test :</label>
            <select id="test-users" class="form-select">
                <option value="" disabled selected>Choisir un utilisateur</option>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <option value="<?php echo htmlspecialchars($utilisateur['mail_uti']); ?>" data-password="<?php echo htmlspecialchars($utilisateur['password_uti']); ?>">
                        <?php echo htmlspecialchars($utilisateur['nom_uti']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <script>
        document.getElementById('test-users').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const email = selectedOption.value;
            const password = selectedOption.getAttribute('data-password');
            document.getElementById('email').value = email;
            document.getElementById('motdepasse').value = password; // Remplit le mot de passe
        });

        // Actualisation en cas d'erreur d'en-tête
        document.addEventListener('DOMContentLoaded', function () {
            const errorElement = document.getElementById('php-error-message');
            if (errorElement && errorElement.textContent.includes('Erreur d\'en-tête détectée')) {
                setTimeout(function () {
                    window.location.reload();
                }, 1000); // Délai de 1 seconde avant l'actualisation
            }
        });
    </script>
</body>
</html>

<?php
ob_end_flush(); // Finaliser le tampon et envoyer la sortie
?>
