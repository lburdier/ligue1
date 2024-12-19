<?php

class GestionNews {

    private $cnx;

    public function __construct($connexion) {
        $this->cnx = $connexion;
    }

    public function createNews($titre, $contenu, $categorie, $image) {
        try {
            $sql = "INSERT INTO news (titre, contenu, categorie, image, date_creation) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$titre, $contenu, $categorie, $image]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de la création de l\'actualité : ' . $e->getMessage());
            return false;
        }
    }

    public function getNewsById($id) {
        try {
            $sql = "SELECT * FROM news WHERE id_news = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return new News(
                    $data['id_news'],
                    $data['titre'],
                    $data['contenu'],
                    $data['categorie'],
                    $data['image'],
                    $data['date_creation']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'actualité : ' . $e->getMessage());
            return null;
        }
    }

    public function getAllNews() {
        try {
            $sql = "SELECT * FROM news ORDER BY date_creation DESC";
            $stmt = $this->cnx->query($sql);
            $newsList = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $newsList[] = new News(
                    $data['id_news'],
                    $data['titre'],
                    $data['contenu'],
                    $data['categorie'],
                    $data['image'],
                    $data['date_creation']
                );
            }

            return $newsList;
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des actualités : ' . $e->getMessage());
            return [];
        }
    }

    public function updateNews($id, $titre, $contenu, $categorie, $image) {
        try {
            $sql = "UPDATE news SET titre = ?, contenu = ?, categorie = ?, image = ? WHERE id_news = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$titre, $contenu, $categorie, $image, $id]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de l\'actualité : ' . $e->getMessage());
            return false;
        }
    }

    public function deleteNews($id) {
        try {
            $sql = "DELETE FROM news WHERE id_news = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression de l\'actualité : ' . $e->getMessage());
            return false;
        }
    }
}

?>