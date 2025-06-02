<?php
header('Content-Type: application/json');

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

// Récupérer les données de la requête
$request = json_decode(file_get_contents('php://input'), true);
$titre = $request['titre'] ?? '';

if (empty($titre)) {
    echo json_encode(['success' => false, 'message' => 'Le titre est requis.']);
    exit;
}

// Appeler l'API Mistral AI pour générer le contenu
$apiUrl = 'https://api.mistral.ai/v1/chat/completions';
$apiKey = 'SBvzOPDnFcovyxhn1ZjM5DHzupsHsm4y';

$messages = [
    [
        "role" => "system",
        "content" => "Tu es un rédacteur d'articles. Rédige un contenu détaillé et engageant basé sur le titre suivant."
    ],
    [
        "role" => "user",
        "content" => $titre
    ]
];

$data = [
    "model" => "mistral-small",
    "messages" => $messages,
    "temperature" => 0.7,
    "max_tokens" => 500
];

$options = [
    "http" => [
        "header" => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
        "method" => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la communication avec l\'API Mistral.']);
    exit;
}

$result = json_decode($response, true);
$content = $result['choices'][0]['message']['content'] ?? '';

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Erreur : Réponse invalide de l\'API Mistral.']);
    exit;
}

echo json_encode(['success' => true, 'content' => $content]);
