<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Récupérer l'ID du club depuis l'URL
    $idClub = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$idClub) {
        echo "<p>Erreur : Aucun ID de club spécifié ou ID invalide.</p>";
        exit;
    }

    // Récupérer les détails du club
    $query = "SELECT c.nom_club, c.ligue_club, s.nom AS nom_stade, s.ville, s.capacite, s.adresse 
              FROM club c
              LEFT JOIN stade s ON c.id_stade = s.id_stade
              WHERE c.id_club = :id_club";
    $stmt = $cnx->prepare($query);
    $stmt->bindParam(':id_club', $idClub, PDO::PARAM_INT);
    $stmt->execute();
    $clubDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clubDetails) {
        echo "<p>Erreur : Club introuvable. Veuillez vérifier que l'ID est correct.</p>";
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur PDO lors de la récupération des détails du club : " . $e->getMessage());
    echo "<p>Erreur : Une erreur est survenue lors de la récupération des données. Détails : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
} catch (Exception $e) {
    error_log("Erreur générale : " . $e->getMessage());
    echo "<p>Erreur : Une erreur inattendue est survenue. Détails : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Inclure la vue pour afficher les détails du club
include __DIR__ . '/../Vues/v_club_details.php';
