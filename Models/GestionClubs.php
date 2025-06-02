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

    /**
     * Get all clubs with their stadium details.
     *
     * @return array
     */
    public function getAllWithStades(): array {
        $sql = "
            SELECT 
                c.id_club, 
                c.nom_club, 
                c.ligue_club, 
                s.nom AS nom_stade, 
                s.ville, 
                s.capacite
            FROM 
                club c
            LEFT JOIN 
                stade s ON c.id_stade = s.id_stade
            ORDER BY 
                c.nom_club ASC
        ";

        $stmt = $this->cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}