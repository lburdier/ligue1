<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

if (!isset($_SESSION['user'])) {
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit();
}

$gestionBDD = new GestionBDD();
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

$utilisateurs = $gestionUtilisateur->getListUtilisateurs();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];

    $success = $gestionUtilisateur->deleteUserById($id);

    if ($success) {
        echo "<script>alert('Utilisateur supprimé avec succès.'); window.location.href='/ligue1/admin_m1';</script>";
        exit();
    } else {
        echo "<script>alert('Erreur lors de la suppression de l\'utilisateur.'); window.location.href='/ligue1/admin_m1';</script>";
        exit();
    }
}

$pageTitle = "Tableau de Bord Admin";
include './Vues/v_admin_dashboard.php';