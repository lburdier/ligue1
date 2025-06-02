<?php
header('Content-Type: application/json');

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

// Récupérer les données de la requête
$request = json_decode(file_get_contents('php://input'), true);
$contenu = $request['contenu'] ?? '';

if (empty($contenu)) {
    echo json_encode(['success' => false, 'message' => 'Le contenu est requis.']);
    exit;
}

// Appeler l'API Mistral AI pour suggérer une catégorie
$apiUrl = 'https://api.mistral.ai/v1/chat/completions';
$apiKey = 'SBvzOPDnFcovyxhn1ZjM5DHzupsHsm4y';

$messages = [
    [
        "role" => "system",
        "content" => "Tu es un assistant qui analyse des articles. Suggère une catégorie appropriée pour l'article suivant. Réponds uniquement par le nom de la catégorie."
    ],
    [
        "role" => "user",
        "content" => $contenu
    ]
];

$data = [
    "model" => "mistral-small",
    "messages" => $messages,
    "temperature" => 0.7,
    "max_tokens" => 10
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
$category = $result['choices'][0]['message']['content'] ?? '';

if (empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Erreur : Réponse invalide de l\'API Mistral.']);
    exit;
}

echo json_encode(['success' => true, 'category' => trim($category)]);
