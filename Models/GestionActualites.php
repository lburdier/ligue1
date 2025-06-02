<?php

class GestionActualites {
    private $cnx;

    public function __construct($cnx) {
        $this->cnx = $cnx;
    }

    /**
     * Fetch general news (where id_club is NULL).
     *
     * @return array
     */
    public function getGeneralNews(): array {
        $sql = "SELECT * FROM actualite WHERE id_club IS NULL ORDER BY date_publication DESC";
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch news related to specific clubs.
     *
     * @param array $clubIds
     * @return array
     */
    public function getNewsByClubs(array $clubIds): array {
        if (empty($clubIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($clubIds), '?'));
        $sql = "SELECT * FROM actualite WHERE id_club IN ($placeholders) ORDER BY date_publication DESC";
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute($clubIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new news entry.
     *
     * @param string $titre
     * @param string $contenu
     * @param string|null $image
     * @param int|null $idClub
     * @return bool
     */
    public function addNews(string $titre, string $contenu, ?string $image, ?int $idClub): bool {
        $sql = "INSERT INTO actualite (titre, contenu, image, id_club, date_publication) 
                VALUES (:titre, :contenu, :image, :id_club, NOW())";
        $stmt = $this->cnx->prepare($sql);
        return $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':image' => $image,
            ':id_club' => $idClub
        ]);
    }

    /**
     * Update an existing news entry.
     *
     * @param int $idActualite
     * @param string $titre
     * @param string $contenu
     * @param string|null $image
     * @param int|null $idClub
     * @return bool
     */
    public function updateNews(int $idActualite, string $titre, string $contenu, ?string $image, ?int $idClub): bool {
        $sql = "UPDATE actualite 
                SET titre = :titre, contenu = :contenu, image = :image, id_club = :id_club 
                WHERE id_actualite = :id_actualite";
        $stmt = $this->cnx->prepare($sql);
        return $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':image' => $image,
            ':id_club' => $idClub,
            ':id_actualite' => $idActualite
        ]);
    }

    /**
     * Delete a news entry.
     *
     * @param int $idActualite
     * @return bool
     */
    public function deleteNews(int $idActualite): bool {
        $sql = "DELETE FROM actualite WHERE id_actualite = :id_actualite";
        $stmt = $this->cnx->prepare($sql);
        return $stmt->execute([':id_actualite' => $idActualite]);
    }

    /**
     * Insert multiple news entries for clubs.
     *
     * @param array $newsData
     * @return bool
     */
    public function insertMultipleNews(array $newsData): bool {
        $sql = "INSERT INTO actualite (titre, contenu, image, id_club, date_publication) 
                VALUES (:titre, :contenu, :image, :id_club, NOW())";
        $stmt = $this->cnx->prepare($sql);

        foreach ($newsData as $news) {
            $stmt->execute([
                ':titre' => $news['titre'],
                ':contenu' => $news['contenu'],
                ':image' => $news['image'],
                ':id_club' => $news['id_club']
            ]);
        }

        return true;
    }
}
