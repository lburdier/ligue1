<?php
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionActualites.php';

try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionActualites = new GestionActualites($cnx);

    $newsData = [
        ['titre' => 'AC Ajaccio News', 'contenu' => 'Actualité pour AC Ajaccio.', 'image' => '/images/ac_ajaccio.jpg', 'id_club' => 11],
        ['titre' => 'Angers News', 'contenu' => 'Actualité pour Angers.', 'image' => '/images/angers.jpg', 'id_club' => 12],
        ['titre' => 'Auxerre News', 'contenu' => 'Actualité pour Auxerre.', 'image' => '/images/auxerre.jpg', 'id_club' => 13],
        ['titre' => 'Brest News', 'contenu' => 'Actualité pour Brest.', 'image' => '/images/brest.jpg', 'id_club' => 14],
        ['titre' => 'Clermont News', 'contenu' => 'Actualité pour Clermont.', 'image' => '/images/clermont.jpg', 'id_club' => 15],
        ['titre' => 'Lens News', 'contenu' => 'Actualité pour Lens.', 'image' => '/images/lens.jpg', 'id_club' => 16],
        ['titre' => 'Lille News', 'contenu' => 'Actualité pour Lille.', 'image' => '/images/lille.jpg', 'id_club' => 5],
        ['titre' => 'Lorient News', 'contenu' => 'Actualité pour Lorient.', 'image' => '/images/lorient.jpg', 'id_club' => 17],
        ['titre' => 'Lyon News', 'contenu' => 'Actualité pour Lyon.', 'image' => '/images/lyon.jpg', 'id_club' => 3],
        ['titre' => 'Marseille News', 'contenu' => 'Actualité pour Marseille.', 'image' => '/images/marseille.jpg', 'id_club' => 2],
        ['titre' => 'Monaco News', 'contenu' => 'Actualité pour Monaco.', 'image' => '/images/monaco.jpg', 'id_club' => 4],
        ['titre' => 'Montpellier News', 'contenu' => 'Actualité pour Montpellier.', 'image' => '/images/montpellier.jpg', 'id_club' => 18],
        ['titre' => 'Nantes News', 'contenu' => 'Actualité pour Nantes.', 'image' => '/images/nantes.jpg', 'id_club' => 7],
        ['titre' => 'Nice News', 'contenu' => 'Actualité pour Nice.', 'image' => '/images/nice.jpg', 'id_club' => 8],
        ['titre' => 'Paris-SG News', 'contenu' => 'Actualité pour Paris-SG.', 'image' => '/images/paris_sg.jpg', 'id_club' => 1],
        ['titre' => 'Reims News', 'contenu' => 'Actualité pour Reims.', 'image' => '/images/reims.jpg', 'id_club' => 10],
        ['titre' => 'Rennes News', 'contenu' => 'Actualité pour Rennes.', 'image' => '/images/rennes.jpg', 'id_club' => 6],
        ['titre' => 'Strasbourg News', 'contenu' => 'Actualité pour Strasbourg.', 'image' => '/images/strasbourg.jpg', 'id_club' => 9],
        ['titre' => 'Toulouse News', 'contenu' => 'Actualité pour Toulouse.', 'image' => '/images/toulouse.jpg', 'id_club' => 19],
        ['titre' => 'Troyes News', 'contenu' => 'Actualité pour Troyes.', 'image' => '/images/troyes.jpg', 'id_club' => 20],
    ];

    $gestionActualites->insertMultipleNews($newsData);
    echo "News inserted successfully.";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
