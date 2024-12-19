<?php

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo "<script>window.location.href='/ligue1/connexion';</script>";
    exit;
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

$gestionBDD = new GestionBDD("BD_ligue1");
$cnx = $gestionBDD->connect();
$gestionUtilisateur = new GestionUtilisateur($cnx);

$id = $_SESSION['user']['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id && !empty($id)) {
        $success = $gestionUtilisateur->deleteUserById($id);

        if ($success) {
            session_destroy();
            echo "<script>alert('Votre compte a été supprimé avec succès.'); window.location.href='/ligue1';</script>";
            exit;
        } else {
            echo "<script>alert('Erreur lors de la suppression de votre compte. Veuillez réessayer.'); window.location.href='/ligue1/profil';</script>";
            exit;
        }
    } else {
        echo "<script>window.location.href='/ligue1';</script>";
        exit;
    }
}

$pageTitle = "Suppression de Compte - Ligue1";
include_once __DIR__ . '/../Vues/v_delete_profil.php';
ob_end_flush();
?>