<?php
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
include_once __DIR__ . '/../Models/GestionClubs.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']['id'])) {
    header('Location: /ligue1/connexion');
    exit;
}

try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    $gestionUtilisateur = new GestionUtilisateur($cnx);
    $gestionClubs = new GestionClubs($cnx);

    $idUtilisateur = $_SESSION['user']['id'];
    $clubsSuivis = $gestionUtilisateur->getClubsSuivis($idUtilisateur); // Fetch followed clubs from `suivi`
    $clubsSuivisIds = array_column($clubsSuivis, 'id_club'); // Extract IDs of followed clubs

    $tab = $gestionClubs->getAllWithStades(); // Fetch all clubs with their stadiums

    $pageTitle = "Liste des Clubs";
    include __DIR__ . '/../Vues/v_clubs.php';
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des clubs : " . $e->getMessage());
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}
