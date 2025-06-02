<?php
header('Content-Type: application/json');

// API key for Mistral AI
$apiKey = 'SBvzOPDnFcovyxhn1ZjM5DHzupsHsm4y';

// Get the user message from the request
$request = json_decode(file_get_contents('php://input'), true);
$userMessage = $request['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['response' => 'Veuillez poser une question.']);
    exit;
}

// Call Mistral AI API
$apiUrl = 'https://api.mistral.ai/v1/chat/completions';
$data = [
    'model' => 'mistral-small',
    'messages' => [
        ['role' => 'system', 'content' => 'Tu es un assistant virtuel.'],
        ['role' => 'user', 'content' => $userMessage]
    ],
    'temperature' => 0.7,
    'max_tokens' => 100
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

if ($response === false) {
    echo json_encode(['response' => 'Une erreur est survenue lors de la communication avec l\'API.']);
    exit;
}

$result = json_decode($response, true);
$chatbotResponse = $result['choices'][0]['message']['content'] ?? 'Je ne peux pas répondre à cette question.';

echo json_encode(['response' => $chatbotResponse]);
