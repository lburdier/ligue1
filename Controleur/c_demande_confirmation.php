<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    echo "<p>Erreur : Vous devez être connecté pour effectuer cette action.</p>";
    exit;
}

// Stocker la demande de rôle dans la session
$_SESSION['demande_role'] = $_POST['role_createur'] ?? '';

// Rediriger vers la page de confirmation
header("Location: /ligue1/Vues/v_confirmation_role.php");
exit;
?>
