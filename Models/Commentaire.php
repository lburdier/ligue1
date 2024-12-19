<?php

class Commentaire {

    private $id;
    private $idArticle;
    private $auteur;
    private $contenu;
    private $dateCommentaire;

    public function __construct($id, $idArticle, $auteur, $contenu, $dateCommentaire) {
        $this->id = $id;
        $this->idArticle = $idArticle;
        $this->auteur = $auteur;
        $this->contenu = $contenu;
        $this->dateCommentaire = $dateCommentaire;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getIdArticle() {
        return $this->idArticle;
    }

    public function getAuteur() {
        return htmlspecialchars($this->auteur, ENT_QUOTES, 'UTF-8');
    }

    public function getContenu() {
        return htmlspecialchars($this->contenu, ENT_QUOTES, 'UTF-8');
    }

    public function getDateCommentaire() {
        return $this->dateCommentaire;
    }

    // Ajouter un commentaire à la base de données
    public static function ajouterCommentaire($cnx, $idArticle, $auteur, $contenu) {
        try {
            // Validation simple pour s'assurer que les entrées ne sont pas vides
            if (empty($idArticle)) {
                throw new Exception("L'identifiant de l'article est requis.");
            }
            if (empty($auteur)) {
                throw new Exception("Le nom de l'auteur est requis.");
            }
            if (empty($contenu)) {
                throw new Exception("Le contenu du commentaire est requis.");
            }

            // Insertion dans la base de données
            $query = $cnx->prepare(
                    "INSERT INTO commentaire (id_article, auteur, contenu, date_commentaire) 
                VALUES (:id_article, :auteur, :contenu, NOW())"
            );
            $query->execute([
                'id_article' => $idArticle,
                'auteur' => $auteur,
                'contenu' => $contenu
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout du commentaire (base de données) : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur de validation du commentaire : " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les commentaires associés à un article par son ID
    public static function getCommentairesByArticleId($cnx, $idArticle) {
        try {
            $query = $cnx->prepare(
                    "SELECT * FROM commentaire 
                WHERE id_article = :id_article 
                ORDER BY date_commentaire DESC"
            );
            $query->execute(['id_article' => $idArticle]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            $commentaires = [];
            foreach ($result as $row) {
                $commentaires[] = new Commentaire(
                        $row['id_commentaire'],
                        $row['id_article'],
                        $row['auteur'],
                        $row['contenu'],
                        $row['date_commentaire']
                );
            }

            return $commentaires;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            return [];
        }
    }

    // Supprimer un commentaire par son ID
    public static function supprimerCommentaire($cnx, $idCommentaire) {
        try {
            $query = $cnx->prepare("DELETE FROM commentaire WHERE id_commentaire = :id_commentaire");
            $query->execute(['id_commentaire' => $idCommentaire]);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire : " . $e->getMessage());
            return false;
        }
    }

    // Compter le nombre de commentaires pour un article
    public static function compterCommentaires($cnx, $idArticle) {
        try {
            $query = $cnx->prepare("SELECT COUNT(*) as total FROM commentaire WHERE id_article = :id_article");
            $query->execute(['id_article' => $idArticle]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des commentaires : " . $e->getMessage());
            return 0;
        }
    }
}

?>
