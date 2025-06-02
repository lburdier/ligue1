<?php
require_once __DIR__ . '/../Models/GestionBDD.php';
require_once __DIR__ . '/../Models/Stade.php';

class StadesController {
    public function index() {
        try {
            $gestionBDD = new GestionBDD("BD_ligue1");
            $pdo = $gestionBDD->connect();

            // Fetch stadiums with their associated clubs
            $stades = Stade::getAllWithClub($pdo);

            // Include the view to display the data
            require __DIR__ . '/../Vues/stades/index.php';
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des stades : " . $e->getMessage());
            echo "<p>Erreur : Impossible de récupérer les données des stades.</p>";
        }
    }
}
