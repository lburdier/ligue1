<?php

class Article {

    private $id;
    private $titre;
    private $contenu;
    private $categorie;
    private $image;
    private $dateCreation;
    private $idUtilisateur; // Ajout de l'attribut propriétaire

    public function __construct($id, $titre, $contenu, $categorie = null, $image = null, $dateCreation = null, $idUtilisateur = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->categorie = $categorie;
        $this->image = $image;
        $this->dateCreation = $dateCreation;
        $this->idUtilisateur = $idUtilisateur; // Initialisation de l'attribut propriétaire
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getContenu() {
        return $this->contenu;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getImage() {
        return $this->image;
    }

    public function getDateCreation() {
        return $this->dateCreation;
    }

    public function getIdUtilisateur() {
        return $this->idUtilisateur; // Getter pour le propriétaire
    }

    // Méthode pour récupérer un article par son ID
    public static function getArticleById($id, $cnx) {
        try {
            $query = $cnx->prepare("SELECT * FROM article WHERE id_article = :id");
            $query->execute(['id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return new self(
                    $data['id_article'],
                    $data['titre'],
                    $data['contenu'],
                    $data['categorie'],
                    $data['image'],
                    $data['date_creation'],
                    $data['id_uti'] // Ajout de l'attribut propriétaire
                );
            }
            return null;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de l'article par ID : " . $e->getMessage());
            return null;
        }
    }

    // Méthode pour récupérer tous les articles
    public static function getAllArticles($cnx) {
        try {
            $query = $cnx->query("SELECT * FROM article ORDER BY date_creation DESC");
            $articles = [];

            while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = new self(
                    $data['id_article'],
                    $data['titre'],
                    $data['contenu'],
                    $data['categorie'],
                    $data['image'],
                    $data['date_creation'],
                    $data['id_uti'] // Ajout de l'attribut propriétaire
                );
            }

            return $articles;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de tous les articles : " . $e->getMessage());
            return [];
        }
    }
}

?>
