<?php

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionClubs.php';
include_once __DIR__ . '/../Models/GestionNews.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
include_once __DIR__ . '/../Models/GestionAbonner.php';
include_once __DIR__ . '/../Models/Club.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$gestionBDD = new GestionBDD("BD_ligue1");

try {
    $cnx = $gestionBDD->connect();

    $gc = new GestionClubs($cnx);
    $tab = $gc->getListClub();

    // Récupérer les actualités existantes
    $query = "SELECT * FROM actualite ORDER BY date_publication DESC LIMIT 6";
    $stmt = $cnx->query($query);
    $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les nouvelles actualités via l'API NewsAPI
    $apiUrl = "https://newsapi.org/v2/everything?q=Ligue%201&language=fr&sortBy=publishedAt&pageSize=3";
    $apiKey = "8d09cdbcb42247bbbab1d436f6629dbb";

    $options = [
        "http" => [
            "header" => "X-Api-Key: $apiKey\r\n",
            "method" => "GET"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($apiUrl, false, $context);

    $newActualites = [];
    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['articles'])) {
            foreach ($data['articles'] as $article) {
                $newActualites[] = [
                    'titre' => $article['title'] ?? 'Actualité Ligue 1',
                    'description' => substr($article['description'] ?? 'Description non disponible.', 0, 100),
                    'image' => $article['urlToImage'] ?? '/ligue1/img/default_news.jpg',
                    'date_publication' => date('Y-m-d H:i:s')
                ];
            }
        }
    } else {
        error_log("Erreur lors de l'appel à l'API NewsAPI : " . error_get_last()['message']);
    }

    // Fusionner les actualités existantes et nouvelles
    $actualites = array_merge($newActualites, $actualites);

    // Récupérer la liste des articles
    $queryArticles = "SELECT * FROM article ORDER BY date_creation DESC";
    $stmtArticles = $cnx->query($queryArticles);
    $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

    // Passer les données à la vue
    $pageTitle = "Page accueil - Ligue1";
    include __DIR__ . '/../Vues/v_accueil.php';

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des actualités ou des articles : " . $e->getMessage());
    $actualites = [];
    $articles = [];
}
