<?php
// Démarrer la mise en mémoire tampon de sortie
ob_start();

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

    // Ajouter les informations de bannissement pour chaque utilisateur
    foreach ($utilisateurs as &$utilisateur) {
        $ip = $utilisateur['ip'] ?? null; // Assurez-vous que l'IP est stockée pour chaque utilisateur
        if ($ip) {
            $banniJusquA = $gestionUtilisateur->isUserBannedByIP($ip);
            $utilisateur['banni_jusqu_a'] = $banniJusquA;
        } else {
            $utilisateur['banni_jusqu_a'] = null;
        }
    }

    // Supprimer un utilisateur si demandé
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
        $userId = intval($_POST['delete_user_id']);
        $gestionUtilisateur->deleteUserById($userId);
        $_SESSION['notification'] = "Utilisateur supprimé avec succès.";
        echo "<script>window.location.href = '/ligue1/admin_m1';</script>";
        exit;
    }

    // Bannir un utilisateur si demandé
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ban_user_id'])) {
        $userId = intval($_POST['ban_user_id']);
        $banDuration = intval($_POST['ban_duration']); // Durée en jours
        $gestionUtilisateur->banUserById($userId, $banDuration);
        $_SESSION['notification'] = "Utilisateur banni pour $banDuration jours.";
        echo "<script>window.location.href = '/ligue1/admin_m1';</script>";
        exit;
    }
} catch (Exception $e) {
    error_log("Erreur lors de la gestion des utilisateurs : " . $e->getMessage());
    $utilisateurs = [];
}

// Inclure la vue du tableau de bord admin version 2
include __DIR__ . '/../Vues/v_admin_dashboard_v2.php';

// Libérer le tampon de sortie
ob_end_flush();
?>