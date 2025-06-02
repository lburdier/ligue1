<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est un administrateur
if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo "Accès refusé : Vous n'avez pas les droits nécessaires pour effectuer cette action.";
    exit;
}

// Vérifier si le fichier à supprimer est spécifié
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rapport_filename'])) {
    $rapportDir = __DIR__ . '/../logs/rapport/';
    $filename = basename($_POST['rapport_filename']); // Empêcher les chemins relatifs
    $filePath = $rapportDir . $filename;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            $_SESSION['notification'] = "Le rapport '$filename' a été supprimé avec succès.";
        } else {
            $_SESSION['notification'] = "Erreur : Impossible de supprimer le rapport '$filename'.";
        }
    } else {
        $_SESSION['notification'] = "Erreur : Le rapport '$filename' n'existe pas.";
    }
} else {
    $_SESSION['notification'] = "Erreur : Aucun fichier spécifié pour la suppression.";
}

// Rediriger vers le tableau de bord admin
header('Location: /ligue1/admin_m1');
exit;
