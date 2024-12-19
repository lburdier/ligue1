<?php

class GestionArticle {

    private $cnx;

    public function __construct($connexion) {
        $this->cnx = $connexion;
    }

    public function createArticle($titre, $contenu, $categorie, $image, $idUtilisateur) {
        try {
            $sql = "INSERT INTO article (titre, contenu, categorie, image, date_creation, id_uti) VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$titre, $contenu, $categorie, $image, $idUtilisateur]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de la création de l\'article : ' . $e->getMessage());
            return false;
        }
    }

    public function getArticleById($id) {
        try {
            $sql = "SELECT * FROM article WHERE id_article = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return new Article(
                        $data['id_article'],
                        $data['titre'],
                        $data['contenu'],
                        $data['categorie'],
                        $data['image'],
                        $data['date_creation'],
                        $data['id_uti'] // Ajout de l'ID de l'utilisateur (propriétaire)
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'article : ' . $e->getMessage());
            return null;
        }
    }

    public function voirArticle($id) {
        try {
            $article = $this->getArticleById($id);
            if ($article) {
                return $article;
            } else {
                throw new Exception('Article non trouvé.');
            }
        } catch (Exception $e) {
            error_log('Erreur lors de la visualisation de l\'article : ' . $e->getMessage());
            return null;
        }
    }

    public function getArticles() {
        try {
            $sql = "SELECT * FROM article ORDER BY date_creation DESC";
            $stmt = $this->cnx->query($sql);
            $articles = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = new Article(
                        $data['id_article'],
                        $data['titre'],
                        $data['contenu'],
                        $data['categorie'],
                        $data['image'],
                        $data['date_creation'],
                        $data['id_uti'] // Ajout de l'ID de l'utilisateur (propriétaire)
                );
            }

            return $articles;
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des articles : ' . $e->getMessage());
            return [];
        }
    }

    public function updateArticle($id, $titre, $contenu, $categorie, $image, $idUtilisateur) {
        try {
            // Vérifier si l'utilisateur est bien le propriétaire de l'article
            $article = $this->getArticleById($id);
            if ($article) {
                if ($article->getIdUtilisateur() === $idUtilisateur) {
                    $sql = "UPDATE article SET titre = ?, contenu = ?, categorie = ?, image = ? WHERE id_article = ?";
                    $stmt = $this->cnx->prepare($sql);
                    $stmt->execute([$titre, $contenu, $categorie, $image, $id]);

                    if ($stmt->rowCount() > 0) {
                        return true; // Mise à jour réussie
                    } else {
                        error_log('Aucune mise à jour effectuée : les valeurs sont peut-être identiques aux existantes.');
                        return false; // Pas de mise à jour effectuée
                    }
                } else {
                    error_log('Modification non autorisée : l\'utilisateur ' . $idUtilisateur . ' n\'est pas le propriétaire de l\'article ' . $id);
                    throw new Exception('Modification non autorisée : l\'utilisateur n\'est pas le propriétaire de l\'article.');
                }
            } else {
                error_log('Article non trouvé avec l\'ID : ' . $id);
                return false; // Article introuvable
            }
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de l\'article : ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function uploadImageArticle($file) {
        // Vérifier si un fichier a été téléchargé
        if (isset($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../img/';
            $maxFileSize = 500 * 1024; // Taille maximale de 500 Ko
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

            // Vérifier la taille du fichier
            if ($file['size'] > $maxFileSize) {
                error_log('Fichier trop volumineux : ' . $file['size'] . ' octets.');
                return null; // Retourne null si le fichier dépasse la taille maximale
            }

            // Vérifier le type MIME réel du fichier
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                error_log('Type MIME non autorisé : ' . $mimeType);
                return null; // Retourne null si le type MIME est non autorisé
            }

            // Vérifier que l'extension correspond au type MIME
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                error_log('Extension de fichier non autorisée : ' . $fileExtension);
                return null; // Retourne null si l'extension est non autorisée
            }

            // Générer un nom unique pour l'image
            $uniqueName = 'image_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
            $filePath = $uploadDir . $uniqueName;

            // Déplacer le fichier téléchargé vers le dossier img
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                return 'img/' . $uniqueName; // Retourne le chemin relatif de l'image
            } else {
                error_log('Erreur lors du déplacement de l\'image téléchargée.');
                return null; // Retourne null si le déplacement échoue
            }
        } else {
            error_log('Aucun fichier valide téléchargé ou erreur de téléchargement.');
            return null; // Retourne null si aucune image n'a été téléchargée
        }
    }

    public function deleteArticle($id) {
        try {
            $sql = "DELETE FROM article WHERE id_article = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression de l\'article : ' . $e->getMessage());
            return false;
        }
    }
}

?>