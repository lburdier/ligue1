<?php


include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionClubs.php';
include_once __DIR__ . '/../Models/GestionNews.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
include_once __DIR__ . '/../Models/GestionAbonner.php';
include_once __DIR__ . '/../Models/Club.php';

$gestionBDD = new GestionBDD("BD_ligue1");

try {
    $cnx = $gestionBDD->connect();
    
    $gc = new GestionClubs($cnx);
    
    $tab = $gc->getListClub();
        $pageTitle = "Les clubs - Ligue1"; // Définir le titre de la page

    include __DIR__ . '/../Vues/v_clubs.php';
    
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}