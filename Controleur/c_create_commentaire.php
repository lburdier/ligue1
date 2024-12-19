<?php
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
    // Si les informations utilisateur ne sont pas disponibles, afficher une erreur
    echo "<p>Erreur : Vous devez être connecté pour ajouter un commentaire.</p>";
    exit;
}

// Récupérer le nom complet de l'utilisateur connecté
$auteur = htmlspecialchars($userData['prenom'] . ' ' . $userData['nom'], ENT_QUOTES, 'UTF-8');

// Vérifier si les données du formulaire sont soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $idArticle = $_POST['id_article'] ?? null;
    $contenu = trim($_POST['contenu'] ?? '');

    // Vérifier les champs requis
    if (empty($idArticle)) {
        echo "<p>Erreur : L'identifiant de l'article est manquant.</p>";
        exit;
    }

    if (empty($contenu)) {
        echo "<p>Erreur : Le contenu du commentaire est requis.</p>";
        exit;
    }

    try {
        // Connexion à la base de données
        $gestionBDD = new GestionBDD("BD_ligue1");
        $cnx = $gestionBDD->connect();

        // Ajouter le commentaire
        $result = Commentaire::ajouterCommentaire($cnx, $idArticle, $auteur, $contenu);

        if ($result) {
            // Redirection avec JavaScript
            echo "<script>
                alert('Votre commentaire a été ajouté avec succès.');
                window.location.href = '/ligue1/voir_article?id=" . htmlspecialchars(urlencode($idArticle), ENT_QUOTES, 'UTF-8') . "';
            </script>";
            exit;
        } else {
            echo "<p>Erreur : Impossible d'ajouter le commentaire. Veuillez réessayer.</p>";
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de la création du commentaire : " . $e->getMessage());
        echo "<p>Erreur : Une erreur est survenue lors de l'ajout du commentaire.</p>";
    } catch (Exception $e) {
        error_log("Erreur générale lors de la création du commentaire : " . $e->getMessage());
        echo "<p>Erreur : Une erreur est survenue lors de l'ajout du commentaire.</p>";
    }
} else {
    echo "<p>Erreur : Requête invalide. Seules les requêtes POST sont autorisées.</p>";
    exit;
}
?>
