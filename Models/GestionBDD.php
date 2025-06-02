<?php

class GestionBDD {

    private string $user;
    private string $pass;
    private string $dsn;
    private PDO $cnx;

    // Constructeur mis à jour avec les bonnes informations
    public function __construct(string $db = 'BD_ligue1', string $user = 'postgres', string $pass = 'P@ssw0rdSIO') {
        $this->user = $user;
        $this->pass = $pass;
        $this->dsn = 'pgsql:host=localhost;dbname=' . $db;
    }

    /**
     * Connexion à la base de données
     * @return PDO
     */
    public function connect(): PDO {
        try {
            // Connexion à la base de données via PDO avec gestion des erreurs
            $this->cnx = new PDO($this->dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Activer les exceptions en cas d'erreur
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Mode de récupération par défaut
            ]);
            return $this->cnx;
        } catch (PDOException $e) {
            // Gérer l'erreur de connexion
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
            die();
        }
    }

    /**
     * Vérifie l'existence des tables nécessaires dans la base de données
     * @param array $requiredTables
     * @return array
     * @throws Exception
     */
    public function checkTablesExist(array $requiredTables): array {
        try {
            $existingTables = [];
            $query = $this->cnx->query("SHOW TABLES");
            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                $existingTables[] = $row[0];
            }

            $missingTables = array_diff($requiredTables, $existingTables);
            if (!empty($missingTables)) {
                error_log("Tables manquantes : " . implode(", ", $missingTables));
                return $missingTables; // Retourne les tables manquantes
            }

            return []; // Toutes les tables existent
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification des tables : " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification des tables.");
        }
    }

    public function createActualiteTableIfNotExists() {
        try {
            $query = "SELECT to_regclass('public.actualite')"; // Vérifie si la table existe
            $stmt = $this->cnx->query($query);
            $tableExists = $stmt->fetchColumn();

            if (!$tableExists) {
                // Créer la table actualite
                $createTableSQL = "
                    CREATE TABLE actualite (
                        id SERIAL PRIMARY KEY,
                        titre VARCHAR(255) NOT NULL,
                        description TEXT NOT NULL,
                        image VARCHAR(255) NOT NULL,
                        date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    );
                ";
                $this->cnx->exec($createTableSQL);
                error_log("Table 'actualite' créée avec succès.");

                // Insérer des données par défaut
                $insertSQL = "
                    INSERT INTO actualite (titre, description, image) VALUES
                    ('Match PSG vs OM', 'Le grand classique de la Ligue 1 se jouera ce dimanche.', 'img/psg_vs_om.jpg'),
                    ('Nouveau sponsor pour la Ligue 1', 'La Ligue 1 annonce un partenariat avec une grande marque.', 'img/sponsor.jpg'),
                    ('Classement mis à jour', 'Découvrez le classement actualisé après la dernière journée.', 'img/classement.jpg');
                ";
                $this->cnx->exec($insertSQL);
                error_log("Données par défaut insérées dans la table 'actualite'.");
            } else {
                error_log("La table 'actualite' existe déjà.");
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la création ou de l'insertion dans la table 'actualite' : " . $e->getMessage());
            throw new Exception("Erreur lors de la gestion de la table 'actualite'.");
        }
    }
}