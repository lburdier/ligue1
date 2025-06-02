<?php
// Remove any restrictive checks that cause 404 errors
// Ensure this file is included only via the router
if (basename($_SERVER['SCRIPT_FILENAME']) !== 'index.php') {
    exit('Access denied');
}

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Commentaire.php';
include_once __DIR__ . '/../Models/Utilisateur.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Inclure le contrôleur de vérification de connexion
$userData = include __DIR__ . '/C_verif_connexion.php';

if (!$userData) {
    echo "<p>Erreur : Vous devez être connecté pour ajouter un commentaire.</p>";
    exit;
}

// Récupérer le nom complet de l'utilisateur connecté
$auteur = htmlspecialchars($userData['prenom'] . ' ' . $userData['nom'], ENT_QUOTES, 'UTF-8');

// Fonction pour vérifier si l'IP est bannie
function isIpBanned($cnx, $ip) {
    try {
        $query = "SELECT banni_jusqu_a FROM banni_ips WHERE ip = :ip AND banni_jusqu_a > NOW()";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['banni_jusqu_a'] : false;
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de la vérification du bannissement IP : " . $e->getMessage());
        throw new Exception("Erreur lors de la vérification du bannissement IP.");
    }
}

// Fonction pour bannir une IP
function banIp($cnx, $ip, $durationInSeconds) {
    try {
        // Vérifier si l'IP existe déjà
        $queryCheck = "SELECT COUNT(*) FROM banni_ips WHERE ip = :ip";
        $stmtCheck = $cnx->prepare($queryCheck);
        $stmtCheck->bindParam(':ip', $ip);
        $stmtCheck->execute();
        $exists = $stmtCheck->fetchColumn();

        if ($exists) {
            // Mettre à jour l'enregistrement existant
            $queryUpdate = "UPDATE banni_ips SET banni_jusqu_a = NOW() + INTERVAL '1 minute' WHERE ip = :ip";
            $stmtUpdate = $cnx->prepare($queryUpdate);
            $stmtUpdate->bindParam(':ip', $ip);
            $stmtUpdate->execute();
        } else {
            // Insérer un nouvel enregistrement
            $queryInsert = "INSERT INTO banni_ips (ip, banni_jusqu_a) VALUES (:ip, NOW() + INTERVAL '1 minute')";
            $stmtInsert = $cnx->prepare($queryInsert);
            $stmtInsert->bindParam(':ip', $ip);
            $stmtInsert->execute();
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO lors du bannissement IP : " . $e->getMessage());
        throw new Exception("Erreur lors de la mise à jour du bannissement IP.");
    }
}

// Vérifier si l'utilisateur est temporairement banni
$userIP = $_SERVER['REMOTE_ADDR'];
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    $bannedUntil = isIpBanned($cnx, $userIP);
    if ($bannedUntil) {
        echo "<div style='
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #dc3545;
            border-radius: 8px;
            background-color:rgba(49, 49, 49, 0.41);
            color: #721c24;
            font-family: Arial, sans-serif;
            text-align: center;
        '>
            <h2>🚫 Accès refusé</h2>
            <p>Votre IP est temporairement bannie. Vous pourrez réessayer après : " . htmlspecialchars($bannedUntil) . ".</p>
            <a href='/ligue1/voir_article?id=" . htmlspecialchars(urlencode($_POST['id_article'] ?? ''), ENT_QUOTES, 'UTF-8') . "'
               style='
                   display: inline-block;
                   margin-top: 15px;
                   padding: 10px 20px;
                   background-color: #dc3545;
                   color: #fff;
                   text-decoration: none;
                   border-radius: 5px;
                   font-weight: bold;
               '>Retour à l'article</a>
        </div>";
        exit;
    }
} catch (Exception $e) {
    error_log("Erreur lors de la vérification du bannissement IP : " . $e->getMessage());
    echo "<p>Erreur : Une erreur est survenue lors de la vérification du bannissement IP.</p>";
    exit;
}

// Fonction pour modérer un commentaire avec l'API de Mistral AI
function modererCommentaire($contenu, $cnx, $ip) {
    $apiUrl = "https://api.mistral.ai/v1/chat/completions";
    $apiKey = "SBvzOPDnFcovyxhn1ZjM5DHzupsHsm4y";

    $messages = [
        [
            "role"    => "system",
            "content" => "Tu es un modérateur de contenu. Réponds UNIQUEMENT par ACCEPT ou REJECT. Écris ACCEPT si le commentaire ne contient rien d'inapproprié ou offensant, sinon écris REJECT.",
        ],
        [
            "role"    => "user",
            "content" => $contenu,
        ],
    ];

    $data = [
        "model"       => "mistral-small",
        "messages"    => $messages,
        "temperature" => 0.0,
        "max_tokens"  => 5,
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json\r\n" .
            "Authorization: Bearer $apiKey\r\n",
            "method"  => "POST",
            "content" => json_encode($data),
            "timeout" => 15,
        ],
    ];

    $context  = stream_context_create($options);
    $response = @file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        $error = error_get_last();
        throw new Exception("Erreur API Mistral : " . $error['message']);
    }

    $result = json_decode($response, true);
    error_log("Réponse complète de l'API : " . json_encode($result));

    if (!isset($result['choices'][0]['message']['content'])) {
        throw new Exception("Réponse invalide de l'API Mistral AI.");
    }

    $moderationResult = strtoupper(trim($result['choices'][0]['message']['content']));
    error_log("Réponse de modération : " . $moderationResult);

    if ($moderationResult !== "ACCEPT" && $moderationResult !== "AC") {
        banIp($cnx, $ip, 60);

        throw new Exception("<div style='
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #dc3545;
            border-radius: 8px;
            background-color:rgba(49, 49, 49, 0.41);
            color: #721c24;
            font-family: Arial, sans-serif;
            text-align: center;
        '>
            <h2>🚫 Rejeté</h2>
            <p>Votre message ne respecte pas les règles de notre site.</p>
            <p>Votre IP est temporairement bannie pendant 1 minute.</p>
            <a href='/ligue1/voir_article?id=" . htmlspecialchars(urlencode($_POST['id_article'] ?? ''), ENT_QUOTES, 'UTF-8') . "'
               style='
                   display: inline-block;
                   margin-top: 15px;
                   padding: 10px 20px;
                   background-color: #dc3545;
                   color: #fff;
                   text-decoration: none;
                   border-radius: 5px;
                   font-weight: bold;
               '>Retour à l'article</a>
        </div>");
    }

    return true;
}

// Vérifier si les données du formulaire sont soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idArticle = $_POST['id_article'] ?? null;
    $contenu = trim($_POST['contenu'] ?? '');

    if (empty($idArticle)) {
        echo "<p>Erreur : L'identifiant de l'article est manquant.</p>";
        exit;
    }

    if (empty($contenu)) {
        echo "<p>Erreur : Le contenu du commentaire est requis.</p>";
        exit;
    }

    try {
        modererCommentaire($contenu, $cnx, $userIP);

        $query = "INSERT INTO commentaire (id_article, auteur, contenu, date_commentaire)
                  VALUES (:id_article, :auteur, :contenu, NOW())";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(':id_article', $idArticle, PDO::PARAM_INT);
        $stmt->bindParam(':auteur', $auteur, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->execute();

        echo "<div style='
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #28a745;
            border-radius: 8px;
            background-color:rgba(49, 49, 49, 0.41);
            color: #155724;
            font-family: Arial, sans-serif;
            text-align: center;
        '>
            <h2>✅ Succès</h2>
            <p>Votre commentaire a été ajouté avec succès.</p>
            <a href='/ligue1/voir_article?id=" . htmlspecialchars(urlencode($idArticle), ENT_QUOTES, 'UTF-8') . "'
               style='
                   display: inline-block;
                   margin-top: 15px;
                   padding: 10px 20px;
                   background-color: #28a745;
                   color: #fff;
                   text-decoration: none;
                   border-radius: 5px;
                   font-weight: bold;
               '>Retour à l'article</a>
        </div>";
        exit;
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de l'ajout du commentaire : " . $e->getMessage());
        echo "<div style='
            margin: 50px auto;
            max-width: 600px;
            padding: 20px;
            border: 2px solid #dc3545;
            border-radius: 8px;
            background-color:rgba(49, 49, 49, 0.41);
            color: #721c24;
            font-family: Arial, sans-serif;
            text-align: center;
        '>
            <h2>🚫 Erreur</h2>
            <p>Une erreur est survenue lors de l'ajout de votre commentaire. Veuillez réessayer plus tard.</p>
            <p>Si le problème persiste, contactez l'administrateur du site.</p>
            <a href='/ligue1/voir_article?id=" . htmlspecialchars(urlencode($idArticle), ENT_QUOTES, 'UTF-8') . "'
               style='
                   display: inline-block;
                   margin-top: 15px;
                   padding: 10px 20px;
                   background-color: #dc3545;
                   color: #fff;
                   text-decoration: none;
                   border-radius: 5px;
                   font-weight: bold;
               '>Retour à l'article</a>
        </div>";
        exit;
    } catch (Exception $e) {
        echo $e->getMessage();
        exit; // Ensure no further output is generated
    }
} else {
    echo "<p>Erreur : Requête invalide. Seules les requêtes POST sont autorisées.</p>";
    exit;
}
?>
