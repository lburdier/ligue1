<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user'])) {
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit();
}

$gestionBDD = new GestionBDD();
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

// Récupérer la liste des utilisateurs
$utilisateurs = $gestionUtilisateur->getListUtilisateurs(); // Assurez-vous que cette méthode renvoie bien un tableau non vide

// Définir la date actuelle pour vérifier l'inactivité
$currentDate = new DateTime();

// Fonction pour vérifier l'inactivité (dernière connexion ou non connecté)
function estInactif($dateInscription, $lastConnexion) {
    global $currentDate;

    // Convertir les dates en objets DateTime
    $dateInscription = new DateTime($dateInscription);
    $lastConnexion = $lastConnexion ? new DateTime($lastConnexion) : null;

    // Condition 1 : Jamais connecté et plus de 3 mois après l'inscription
    if (is_null($lastConnexion) && $currentDate->diff($dateInscription)->m >= 3) {
        return true;
    }

    // Condition 2 : Dernière connexion il y a plus de 3 mois
    if (!is_null($lastConnexion) && $currentDate->diff($lastConnexion)->m >= 3) {
        return true;
    }

    return false;
}

// Vérifier si une requête POST est envoyée pour supprimer un utilisateur inactif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];

    // Supprimer uniquement si l'utilisateur est inactif
    $utilisateur = $gestionUtilisateur->getUserById($id);

    if ($utilisateur && estInactif($utilisateur['date_inscription'], $utilisateur['last_connexion'])) {
        $success = $gestionUtilisateur->deleteUserById($id);

        if ($success) {
            echo "<script>alert('Utilisateur inactif supprimé avec succès.'); window.location.href='/ligue1/admin_m1';</script>";
            exit();
        } else {
            echo "<script>alert('Erreur lors de la suppression de l\'utilisateur.'); window.location.href='/ligue1/admin_m1';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Cet utilisateur n\'est pas inactif.'); window.location.href='/ligue1/admin_m1';</script>";
        exit();
    }
}

$pageTitle = "Tableau de Bord Admin";
include './Vues/v_admin_dashboard_v2.php';
?>