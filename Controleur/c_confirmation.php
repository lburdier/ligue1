<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si le nom est présent dans la session
$nom = $_SESSION['nom'] ?? 'Utilisateur'; // Utiliser "Utilisateur" par défaut si le nom n'est pas trouvé

// Inclure la vue pour afficher la page de confirmation
$pageTitle = "Confirmation - Ligue1"; // Définir le titre de la page
include './Vues/v_confirmation.php';
?>