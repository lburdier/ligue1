<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion à la base de données
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php'; // Inclure la classe GestionUtilisateur
// Instancier la classe GestionBDD et établir la connexion à la base de données
$gestionBDD = new GestionBDD();
$pdo = $gestionBDD->connect();

if (!$pdo) {
    die("Erreur de connexion à la base de données.");
}

// Instancier GestionUtilisateur pour vérifier l'existence de l'email
$gestionUtilisateur = new GestionUtilisateur($pdo);

// Initialiser les messages d'erreur
$errorEmail = '';
$errorIdClub = '';
$erreurs = [];

// Récupération des données du formulaire ou des variables de session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // S'assurer que les champs sont définis avant de les stocker dans la session
    $_SESSION['prenom'] = !empty($_POST['prenom']) ? htmlspecialchars(trim($_POST['prenom'])) : '';
    $_SESSION['nom'] = !empty($_POST['nom']) ? htmlspecialchars(trim($_POST['nom'])) : '';
    $_SESSION['email'] = !empty($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : '';
    $_SESSION['sexe'] = $_POST['sexe'] ?? '';
    $_SESSION['id_club'] = $_POST['id_club'] ?? '';
    $_SESSION['image'] = $_POST['image'] ?? '';

    // Vérification si l'email existe déjà
    if (!empty($_SESSION['email']) && $gestionUtilisateur->emailExists($_SESSION['email'])) {
        $errorEmail = 'Cet email est déjà utilisé.';
    }
}

// Récupérer les valeurs de la session pour les réutiliser
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';
$email = $_SESSION['email'] ?? '';
$sexe = $_SESSION['sexe'] ?? '';
$id_club = $_SESSION['id_club'] ?? null;
$image = $_SESSION['image'] ?? '';

// Valider si id_club est sélectionné
if (empty($id_club)) {
    $errorIdClub = "Veuillez sélectionner un club."; // Ajoute un message d'erreur
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Ligue1'; ?></title>
        <link rel="stylesheet" href="style/style.css">
        <style>
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

            .avatar {
                width: 50px;
                margin-right: 10px;
                cursor: pointer;
            }

            .input-error {
                border-color: red;
                background-color: #ffe9e9;
            }

        </style>
    </head>
    <body>

        <div class="container mt-5">
            <h1 class="mb-4">Inscription</h1>

            <!-- Formulaire d'inscription -->
            <form id="inscription-form" action="" method="POST">
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required maxlength="10" pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+" title="Le prénom ne doit contenir que des lettres." placeholder="Saisir votre prénom">
                    <span id="message-prenom" class="error"></span>
                </div>

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required maxlength="10" pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+" title="Le nom ne doit contenir que des lettres." placeholder="Saisir votre nom">
                    <span id="message-nom" class="error"></span>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control <?php echo!empty($errorEmail) ? 'email-error' : ''; ?>" 
                           id="email" name="email" 
                           value="<?php echo htmlspecialchars($email); ?>" 
                           type="email" lang="fr" maxlength="254" 
                           placeholder="example@domain.com" autocapitalize="off" spellcheck="false" 
                           autocomplete="on" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|fr)$" 
                           title="L'email doit commencer par des lettres ou des chiffres, suivi d'un @ et se terminer par .com ou .fr.">

                    <span id="message-email" class="error"><?php echo!empty($errorEmail) ? $errorEmail : ''; ?></span>
                </div>



                <div class="mb-3">
                    <label for="motdepasse" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="motdepasse" name="motdepasse" required pattern="(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                               title="Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial." placeholder="Saisir mot de passe">
                        <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">Générer</button>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                        <label class="form-check-label" for="showPassword">
                            Afficher le mot de passe
                        </label>
                    </div>
                    <span id="message-motdepasse" class="error"></span>
                </div>

                <div class="mb-3">
                    <label for="motdepasse_confirmation" class="form-label">Confirmez votre mot de passe</label>
                    <input type="password" class="form-control" id="motdepasse_confirmation" name="motdepasse_confirmation" required
                           placeholder="Confirmez votre mot de passe">
                    <span id="message-motdepasse-confirmation" class="error"></span>
                </div>

                <div class="mb-3">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select class="form-select" id="sexe" name="sexe" required>
                        <option value="" disabled <?php echo ($sexe == '') ? 'selected' : ''; ?>>Choisissez votre sexe</option>
                        <option value="Homme" <?php echo ($sexe == 'Homme') ? 'selected' : ''; ?>>Homme</option>
                        <option value="Femme" <?php echo ($sexe == 'Femme') ? 'selected' : ''; ?>>Femme</option>
                        <option value="Autre" <?php echo ($sexe == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                    <span id="message-sexe" class="error"></span>
                </div>

                <div class="mb-3">
                    <label for="id_club" class="form-label">Choisissez votre club favori</label>
                    <select class="form-select" id="id_club" name="id_club" required>
                        <option value="" disabled <?php echo ($id_club == '') ? 'selected' : ''; ?>>Sélectionnez un club</option>
                        <?php if (!empty($tab)): ?>
                            <?php foreach ($tab as $club): ?>
                                <option value="<?php echo htmlspecialchars($club->getId()); ?>" <?php echo ($id_club == $club->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($club->getNom()); ?> - <?php echo htmlspecialchars($club->getEmplacement()); ?> <?php echo htmlspecialchars($club->getLigue()); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Aucun club trouvé.</option>
                        <?php endif; ?>
                    </select>
                    <span id="message-id_club" class="error"></span>
                </div>


                <div class="mb-3">
                    <label for="avatar" class="form-label">Choisissez votre avatar</label><br>
                    <input type="radio" id="avatar1" name="image" value="avatars/femelle.png" <?php echo ($image == 'avatars/femelle.png') ? 'checked' : ''; ?> required>
                    <label for="avatar1"><img src="avatars/femelle.png" alt="Avatar Femme" class="avatar"></label>

                    <input type="radio" id="avatar2" name="image" value="avatars/pere.png" <?php echo ($image == 'avatars/pere.png') ? 'checked' : ''; ?> required>
                    <label for="avatar2"><img src="avatars/pere.png" alt="Avatar Homme" class="avatar"></label>

                    <input type="radio" id="avatar3" name="image" value="avatars/profil.png" <?php echo ($image == 'avatars/profil.png') ? 'checked' : ''; ?> required>
                    <label for="avatar3"><img src="avatars/profil.png" alt="Avatar Profil" class="avatar"></label>
                    <span id="message-avatar" class="error"></span>
                </div>

                <button type="submit" class="btn btn-primary" name="submit" value="submit">S'inscrire</button>
            </form>
        </div>

        <script>
            // Fonction pour sauvegarder les données du formulaire dans le Local Storage
            function saveToLocalStorage() {
                var formElements = document.querySelectorAll('#inscription-form input, #inscription-form select');
                formElements.forEach(function (element) {
                    if (element.name && element.value) { // S'assurer que le nom et la valeur existent
                        localStorage.setItem(element.name, element.value);
                    }
                });
            }

            // Restaurer les données à partir du Local Storage
            function loadFromLocalStorage() {
                var formElements = document.querySelectorAll('#inscription-form input, #inscription-form select');
                formElements.forEach(function (element) {
                    if (localStorage.getItem(element.name)) {
                        element.value = localStorage.getItem(element.name);
                    }
                });
            }

            // Sauvegarde automatique à chaque changement
            document.querySelectorAll('#inscription-form input, #inscription-form select').forEach(function (element) {
                element.addEventListener('input', saveToLocalStorage);
            });

            // Charger les données du Local Storage au chargement de la page
            document.addEventListener('DOMContentLoaded', loadFromLocalStorage);

            // Fonction de validation du formulaire
            function validerFormulaire() {
                var valid = true;

                // Réinitialiser les messages d'erreur
                document.querySelectorAll('.error').forEach(function (errorSpan) {
                    errorSpan.textContent = '';
                });

                // Valider le mot de passe
                var motdepasse = document.getElementById('motdepasse').value;
                var motdepassePattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (!motdepassePattern.test(motdepasse)) {
                    document.getElementById('message-motdepasse').textContent = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.';
                    valid = false;
                }

                return valid;
            }

            // Fonction pour générer un mot de passe respectant le pattern
            function generatePassword() {
                var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var lowercase = 'abcdefghijklmnopqrstuvwxyz';
                var digits = '0123456789';
                var specialChars = '@$!%*?&';

                var password = '';

                // Assurer qu'il y ait au moins une majuscule
                password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));

                // Assurer qu'il y ait au moins une minuscule
                password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));

                // Assurer qu'il y ait au moins un chiffre
                password += digits.charAt(Math.floor(Math.random() * digits.length));

                // Assurer qu'il y ait au moins un caractère spécial
                password += specialChars.charAt(Math.floor(Math.random() * specialChars.length));

                // Remplir les caractères restants (jusqu'à 8 ou plus) avec des lettres, des chiffres et des caractères spéciaux
                var allChars = uppercase + lowercase + digits + specialChars;
                for (var i = password.length; i < 12; i++) {
                    password += allChars.charAt(Math.floor(Math.random() * allChars.length));
                }

                // Mélanger les caractères du mot de passe pour éviter un ordre prévisible
                password = shufflePassword(password);

                // Remplir le champ de mot de passe avec le mot de passe généré
                document.getElementById('motdepasse').value = password;
            }

            // Fonction pour mélanger aléatoirement les caractères d'une chaîne
            function shufflePassword(password) {
                var array = password.split('');
                for (var i = array.length - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var temp = array[i];
                    array[i] = array[j];
                    array[j] = temp;
                }
                return array.join('');
            }

            // Fonction pour afficher/masquer le mot de passe
            function togglePasswordVisibility() {
                var passwordField = document.getElementById('motdepasse');
                var showPassword = document.getElementById('showPassword');
                if (showPassword.checked) {
                    passwordField.type = 'text'; // Afficher en clair
                } else {
                    passwordField.type = 'password'; // Masquer le mot de passe
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                var motdepasse = document.getElementById('motdepasse');
                var motdepasseConfirmation = document.getElementById('motdepasse_confirmation');
                var messageConfirmation = document.getElementById('message-motdepasse-confirmation');

                // Fonction pour vérifier si les mots de passe correspondent
                function checkPasswordMatch() {
                    if (motdepasse.value !== motdepasseConfirmation.value) {
                        messageConfirmation.textContent = 'Les mots de passe ne correspondent pas.';
                        motdepasseConfirmation.classList.add('input-error');
                    } else {
                        messageConfirmation.textContent = '';
                        motdepasseConfirmation.classList.remove('input-error');
                    }
                }

                // Ajouter un écouteur pour vérifier la correspondance des mots de passe en temps réel sur les deux champs
                motdepasse.addEventListener('input', checkPasswordMatch);
                motdepasseConfirmation.addEventListener('input', checkPasswordMatch);
            });
        </script>
    </body>
</html>