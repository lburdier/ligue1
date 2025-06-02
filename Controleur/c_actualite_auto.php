<?php

include_once __DIR__ . '/../Models/GestionBDD.php';

// URL de l'API pour récupérer les actualités de la Ligue 1
$apiUrl = "https://newsapi.org/v2/everything?q=Ligue%201&language=fr&sortBy=publishedAt&pageSize=1";
$apiKey = "8d09cdbcb42247bbbab1d436f6629dbb"; // Utiliser la clé API fournie

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Appeler l'API pour récupérer les actualités
    $options = [
        "http" => [
            "header" => "Authorization: Bearer $apiKey\r\n",
            "method" => "GET"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($apiUrl, false, $context);

    if ($response === false) {
        throw new Exception("Erreur lors de la récupération des données de l'API.");
    }

    $data = json_decode($response, true);

    // Vérifier si des actualités sont disponibles
    if (!empty($data['articles'])) {
        $article = $data['articles'][0]; // Prendre la première actualité
        $titre = $article['title'] ?? 'Actualité Ligue 1';
        $description = $article['description'] ?? 'Description non disponible.';
        $image = $article['urlToImage'] ?? '/ligue1/img/default_news.jpg';

        // Insérer ou mettre à jour l'actualité quotidienne dans la table
        $query = "
            INSERT INTO actualite (titre, description, image, date_publication)
            VALUES (:titre, :description, :image, NOW())
            ON CONFLICT (date_publication::date) DO UPDATE
            SET titre = EXCLUDED.titre, description = EXCLUDED.description, image = EXCLUDED.image
        ";
        $stmt = $cnx->prepare($query);
        $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':image' => $image
        ]);
    }
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
}
