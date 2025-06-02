<?php

// Démarre la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion de la classe de connexion à la base (utile si élargissement plus tard)
require_once __DIR__ . '/../Models/GestionBDD.php';

// Chargement manuel de la clé API si elle n'existe pas encore
if (!isset($_ENV['SPORTS_API_KEY']) && file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env');
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, 'SPORTS_API_KEY=')) {
            $_ENV['SPORTS_API_KEY'] = substr($line, strlen('SPORTS_API_KEY='));
            break;
        }
    }
}

$apiKey = $_ENV['SPORTS_API_KEY'] ?? '';
$apiUrl = "https://api.sportsdata.io/v4/soccer/scores/json/GamesByDate";

$matches = [];
$errorMessage = "";

// Si aucune clé API, on affiche une erreur
if (empty($apiKey)) {
    $errorMessage = "Clé API absente ou invalide. Veuillez contacter l'administrateur.";
    include __DIR__ . '/../Vues/v_live_matches.php';
    exit;
}

// Préparer la date du jour
$today = date('Y-m-d');
$apiEndpoint = "$apiUrl/$today";

// Options HTTP avec en-tête pour clé API
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Ocp-Apim-Subscription-Key: $apiKey\r\n"
    ]
];
$context = stream_context_create($options);

// Premier appel API : aujourd'hui
$response = @file_get_contents($apiEndpoint, false, $context);

if ($response !== false) {
    $matches = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($matches)) {
        $matches = [];
        $errorMessage = "Erreur de décodage des données JSON reçues.";
    } elseif (empty($matches)) {
        // Si vide, fallback sur les matchs passés
        $pastDate = date('Y-m-d', strtotime('-7 days'));
        $fallbackEndpoint = "$apiUrl/$pastDate";
        $fallbackResponse = @file_get_contents($fallbackEndpoint, false, $context);

        if ($fallbackResponse !== false) {
            $matches = json_decode($fallbackResponse, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($matches)) {
                $errorMessage = "Aucun match en direct aujourd’hui. Voici ceux de la semaine dernière.";
            } else {
                $matches = [];
                $errorMessage = "Aucun match trouvé (ni en direct, ni dans les archives).";
            }
        } else {
            $matches = [];
            $errorMessage = "Impossible de contacter l’API pour les matchs précédents.";
        }
    }
} else {
    $errorMessage = "Impossible de récupérer les matchs en direct. L’API ne répond pas.";
}

// Enfin, inclusion de la vue pour l'affichage
include __DIR__ . '/../Vues/v_live_matches.php';