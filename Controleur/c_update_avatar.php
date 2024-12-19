<?php
// Démarrer le tampon de sortie
ob_start();

// Vérifiez si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Redirection via JavaScript si l'utilisateur n'est pas connecté
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit;
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Initialiser la base de données
$gestionBDD = new GestionBDD("BD_ligue1");
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

// Récupérer l'ID de l'utilisateur depuis la session
$id = $_SESSION['user']['id'] ?? null;

// Vérifiez si l'utilisateur est connecté
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avatar = $_POST['avatar'] ?? '';

    // Valider que l'avatar n'est pas vide
    if (!empty($avatar)) {
        // Mettre à jour l'image de l'utilisateur
        $success = $gestionUtilisateur->updateImageUser($id, $avatar);
        if ($success) {
            // Mettre à jour la session avec le nouvel avatar
            $_SESSION['user']['image'] = $avatar;
            echo "<script>alert('Avatar mis à jour avec succès.'); window.location.href='/ligue1/profil';</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l\'avatar.'); window.location.href='/ligue1/profil';</script>";
        }
    } else {
        echo "<script>alert('Veuillez sélectionner un avatar.'); window.location.href='/ligue1/profil';</script>";
    }
}

// Fin du tampon de sortie
ob_end_flush();
?>