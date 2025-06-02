<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est un administrateur
if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo "Accès refusé : Vous n'avez pas les droits nécessaires pour accéder à cette page.";
    exit;
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);

    // Récupérer la liste des utilisateurs
    $utilisateurs = $gestionUtilisateur->getListUtilisateurs();
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
    $utilisateurs = [];
}

// Inclure la vue du tableau de bord admin
include __DIR__ . '/../Vues/v_admin_dashboard.php';
?>