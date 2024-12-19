<?php

class News {

    private $id;
    private $idClub;
    private $article;
    private $dateCreation;

    public function __construct($id, $idClub, $article, $dateCreation = null) {
        $this->id = $id;
        $this->idClub = $idClub;
        $this->article = $article;
        $this->dateCreation = $dateCreation;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdClub() {
        return $this->idClub;
    }

    public function getArticle() {
        return $this->article;
    }

    public function getDateCreation() {
        return $this->dateCreation;
    }

    public static function getNewsById($id, $cnx) {
        $query = $cnx->prepare("SELECT * FROM news WHERE id_news = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new News($data['id_news'], $data['id_club'], $data['article_news'], $data['date_creation']);
        }
        return null;
    }

    public static function getAllNews($cnx) {
        $query = $cnx->query("SELECT * FROM news");
        $newsList = [];

        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
            $newsList[] = new News($data['id_news'], $data['id_club'], $data['article_news'], $data['date_creation']);
        }

        return $newsList;
    }
}

?>