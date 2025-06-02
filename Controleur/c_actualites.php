<?php

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
include_once __DIR__ . '/../Models/GestionActualites.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']['id'])) {
    header('Location: /ligue1/connexion');
    exit;
}

try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    $gestionUtilisateur = new GestionUtilisateur($cnx);
    $gestionActualites = new GestionActualites($cnx);

    $idUtilisateur = $_SESSION['user']['id'];

    // Fetch IDs of clubs followed by the user from the `suivi` table
    $clubsSuivis = $gestionUtilisateur->getClubsSuivis($idUtilisateur);
    $clubsSuivisIds = array_column($clubsSuivis, 'id_club');

    // Fetch general news
    $generalNews = $gestionActualites->getGeneralNews();

    // Fetch news related to followed clubs
    $actualites = $gestionActualites->getNewsByClubs($clubsSuivisIds);

    $pageTitle = "Actualités";
    include __DIR__ . '/../Vues/v_actualites.php';

} catch (Exception $e) {
    error_log("Erreur lors de la récupération des actualités : " . $e->getMessage());
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
?>
