<?php
ob_start();

// Ensure $erreurs and $utilisateurs are defined to avoid undefined variable errors
$erreurs = $erreurs ?? [];
$utilisateurs = $utilisateurs ?? [];

$pageTitle = "Ligue1 - Connexion";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/ligue1/style/style.css?v=<?= time(); ?>"> <!-- Use the correct CSS file -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <style>
        .error { color: red; }
        .success { color: green; }
        .captcha-image {
            width: 100px; height: 100px; margin: 5px; cursor: pointer;
            border-radius: 8px; border: 3px solid transparent; transition: transform 0.2s; object-fit: cover;
        }
        .captcha-image:hover { transform: scale(1.05); }
        .selected { border-color: blue; }
        .correct { border-color: green !important; }
        .incorrect { border-color: red !important; }
        .captcha-container { display: flex; flex-wrap: wrap; justify-content: center; }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../menu.php'; ?>
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

        <form action="/ligue1/connexion" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mot de passe</label>
                <input type="password" id="motdepasse" name="motdepasse" class="form-control" required>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-secondary" onclick="initCaptcha()">Démarrer le Captcha</button>
                <div id="captcha-instructions" class="mt-2"></div>
                <div id="captcha-images" class="captcha-container"></div>
                <div id="captcha-result"></div>
                <input type="hidden" id="captcha-valid" name="captcha_valid" value="0">
            </div>

            <button type="submit" class="btn btn-primary mb-4">Se connecter</button>
        </form>

        <p>Pas encore inscrit? <a href="/ligue1/inscription">Créer un compte</a></p>

        <div class="mt-4">
            <label>Déboguer avec un utilisateur de test :</label>
            <select id="test-users" class="form-select">
                <option disabled selected>Choisir un utilisateur</option>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <option value="<?= htmlspecialchars($utilisateur['mail_uti']); ?>"
                            data-password="<?= htmlspecialchars($utilisateur['password_uti']); ?>">
                        <?= htmlspecialchars($utilisateur['nom_uti']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <script>
    const modelURL = "/ligue1/model-ai/model.json";
    const metadataURL = "/ligue1/model-ai/metadata.json";
    let model;

    async function loadModel() {
        if (!model) model = await tmImage.load(modelURL, metadataURL);
    }

    async function classifySingleImage(path) {
        const img = new Image();
        img.src = path;
        await new Promise(resolve => img.onload = resolve);

        const predictions = await model.predict(img);
        return predictions.sort((a, b) => b.probability - a.probability)[0];
    }

    async function classifyImages() {
        const promises = [];
        for (let i = 1; i <= 50; i++) {
            const path = `/ligue1/img/dossier-captcha/${i}.jpg`;
            promises.push(classifySingleImage(path).then(prediction => ({
                src: path,
                category: prediction.className,
                probability: prediction.probability
            })));
        }
        return await Promise.all(promises);
    }

    async function initCaptcha() {
        document.getElementById('captcha-result').innerHTML = '';
        document.getElementById('captcha-valid').value = '0';

        await loadModel();

        const categories = ["banane", "pomme", "kiwi", "ananas", "cerise"];
        const fruitDemande = categories[Math.floor(Math.random() * categories.length)];
        document.getElementById("captcha-instructions").innerHTML = 
            `Sélectionnez les 3 images correspondant au fruit : <strong>${fruitDemande}</strong>`;

        const classifiedImages = await classifyImages();
        const correctImages = classifiedImages.filter(i => i.category === fruitDemande);
        const incorrectImages = classifiedImages.filter(i => i.category !== fruitDemande);

        const selectedImages = [
            ...correctImages.sort(() => 0.5 - Math.random()).slice(0, 3),
            ...incorrectImages.sort(() => 0.5 - Math.random()).slice(0, 3)
        ].sort(() => 0.5 - Math.random());

        const captchaContainer = document.getElementById("captcha-images");
        captchaContainer.innerHTML = '';

        let selections = [];

        selectedImages.forEach((image, index) => {
            const img = document.createElement("img");
            img.src = image.src;
            img.className = 'captcha-image';
            img.onclick = () => {
                if (selections.length >= 3 || img.classList.contains('selected')) return;

                img.classList.add('selected', image.category === fruitDemande ? 'correct' : 'incorrect');
                selections.push(image.category === fruitDemande);

                if (selections.length === 3) {
                    const success = selections.every(sel => sel);
                    document.getElementById('captcha-result').innerHTML = success
                        ? '<div class="success">✅ Captcha validé !</div>'
                        : '<div class="error">❌ Captcha échoué. Veuillez réessayer.</div>';
                    document.getElementById('captcha-valid').value = success ? '1' : '0';

                    document.querySelectorAll('.captcha-image').forEach(img => img.onclick = null);
                }
            };
            captchaContainer.appendChild(img);
        });
    }

    document.getElementById('test-users').addEventListener('change', function () {
        document.getElementById('email').value = this.value;
        document.getElementById('motdepasse').value = this.selectedOptions[0].dataset.password;
    });
    </script>

</body>
</html>

<?php
ob_end_flush();
