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
}