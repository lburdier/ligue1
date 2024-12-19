<?php

// Assurez-vous d'inclure le fichier de la classe Club
include_once __DIR__ . '/../Models/Club.php';

class GestionClubs {

    private PDO $cnx;

    public function __construct(PDO $cnx) {
        $this->cnx = $cnx;
    }

    function getListClub(): array {
        $res = $this->cnx->query("SELECT id_club, nom_club, ligue_club FROM club");
        $tabResultat = [];

        while ($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            if (isset($ligne['id_club'], $ligne['nom_club'], $ligne['ligue_club'])) {
                // Créez une instance de Club avec le nom et la ligue
                $club = new Club($ligne['nom_club'], $ligne['ligue_club']);
                $club->setId($ligne['id_club']); // Assurez-vous que cette méthode existe dans la classe Club
                $tabResultat[] = $club;
            } else {
                // Message d'erreur si des données sont manquantes
                echo "Missing data for club: " . json_encode($ligne) . "\n";
            }
        }
        return $tabResultat;
    }
}