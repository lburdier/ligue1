<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: /ligue1/connexion');
    exit;
}

include_once __DIR__ . '/../Models/GestionBDD.php';

try {
    // Initialize database connection and utilities
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Retrieve user and club IDs
    $idUtilisateur = $_SESSION['user']['id'];
    $idClub = filter_input(INPUT_POST, 'id_club', FILTER_VALIDATE_INT);
    $suivre = isset($_POST['suivre']); // Checkbox value (true if checked)

    if (!$idClub) {
        throw new Exception("ID du club invalide.");
    }

    // Check if the user is already following the club
    $stmt = $cnx->prepare("SELECT 1 FROM suivi WHERE id_utilisateur = ? AND id_club = ?");
    $stmt->execute([$idUtilisateur, $idClub]);

    if ($stmt->rowCount() > 0) {
        if (!$suivre) {
            // Unfollow the club
            $deleteStmt = $cnx->prepare("DELETE FROM suivi WHERE id_utilisateur = ? AND id_club = ?");
            $deleteStmt->execute([$idUtilisateur, $idClub]);
        }
    } else {
        if ($suivre) {
            // Follow the club
            $insertStmt = $cnx->prepare("INSERT INTO suivi (id_utilisateur, id_club, date_suivi) VALUES (?, ?, NOW())");
            $insertStmt->execute([$idUtilisateur, $idClub]);
        }
    }

    // Redirect to the clubs page
    header('Location: /ligue1/clubs');
    exit;
} catch (Exception $e) {
    // Log the error and set a session message
    error_log("Erreur lors de la mise à jour du suivi : " . $e->getMessage());
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
?>
