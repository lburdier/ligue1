<?php
class Stade {
    public static function getAllWithClub(PDO $pdo) {
        $sql = "SELECT c.nom_club, s.nom AS nom_stade, s.ville, s.capacite
                FROM club c
                JOIN stade s ON c.id_stade = s.id_stade
                ORDER BY c.nom_club ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
