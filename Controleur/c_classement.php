<?php

include_once __DIR__ . '/../Models/GestionBDD.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL de l'API officielle pour récupérer le classement de la Ligue 1
$apiUrl = "https://api.football-data.org/v4/competitions/FL1/standings";
$apiKey = "a78cb11b4f304902a23731cff647a4fa"; // Utiliser la clé API fournie

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Appeler l'API pour récupérer le classement
    $options = [
        "http" => [
            "header" => "X-Auth-Token: $apiKey\r\n",
            "method" => "GET"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        throw new Exception("Erreur lors de la récupération des données de l'API.");
    }

    $data = json_decode($response, true);

    // Extraire les données du classement
    $classement = [];
    if (isset($data['standings'][0]['table'])) {
        // Vider la table classement avant d'insérer les nouvelles données
        $cnx->exec("TRUNCATE TABLE classement");

        foreach ($data['standings'][0]['table'] as $team) {
            $classement[] = [
                'position' => $team['position'] ?? null,
                'nom_club' => $team['team']['name'] ?? 'Inconnu',
                'points' => $team['points'] ?? 0,
                'victoires' => $team['won'] ?? 0,
                'defaites' => $team['lost'] ?? 0,
                'matchs_nuls' => $team['draw'] ?? 0
            ];

            // Insérer les données dans la table classement
            $query = "INSERT INTO classement (position, nom_club, points, victoires, defaites, matchs_nuls, date_mise_a_jour)
                      VALUES (:position, :nom_club, :points, :victoires, :defaites, :matchs_nuls, NOW())";
            $stmt = $cnx->prepare($query);
            $stmt->execute([
                ':position' => $team['position'] ?? null,
                ':nom_club' => $team['team']['name'] ?? 'Inconnu',
                ':points' => $team['points'] ?? 0,
                ':victoires' => $team['won'] ?? 0,
                ':defaites' => $team['lost'] ?? 0,
                ':matchs_nuls' => $team['draw'] ?? 0
            ]);
        }
    }

    $pageTitle = "Classement Ligue 1";

    // Inclure la vue pour afficher le classement
    include __DIR__ . '/../Vues/v_classement.php';

} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    $classement = [];
    $pageTitle = "Classement Ligue 1 - Erreur";
    include __DIR__ . '/../Vues/v_classement.php';
}
