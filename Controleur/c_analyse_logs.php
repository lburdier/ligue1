<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est un administrateur
if ($_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo "Accès refusé : Vous n'avez pas les droits nécessaires pour accéder à cette page.";
    exit;
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';

// Chemins des fichiers de logs
$logDir = __DIR__ . '/../logs/';
$rapportDir = $logDir . 'rapport/';
$loginSuccessLog = $logDir . 'login_success.log';
$loginFailureLog = $logDir . 'login_failure.log';
$errorLog = ini_get('error_log');

// Vérifier et créer le dossier des rapports s'il n'existe pas
if (!is_dir($rapportDir)) {
    mkdir($rapportDir, 0755, true);
}

// Fonction pour analyser les logs avec l'API Mistral
function analyserLogsAvecMistral($logs) {
    $apiUrl = 'https://api.mistral.ai/v1/chat/completions';
    $apiKey = 'SBvzOPDnFcovyxhn1ZjM5DHzupsHsm4y';

    $messages = [
        [
            "role" => "system",
            "content" => "Tu es un analyste de logs. Analyse les logs suivants et génère un rapport détaillé des anomalies, tendances et recommandations."
        ],
        [
            "role" => "user",
            "content" => $logs
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
        return "Erreur : Impossible de communiquer avec l'API Mistral.";
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? "Erreur : Réponse invalide de l'API Mistral.";
}

// Lire les logs et les analyser
$logs = '';
if (file_exists($loginSuccessLog)) {
    $logs .= "=== Logs de connexions réussies ===\n" . file_get_contents($loginSuccessLog) . "\n\n";
}
if (file_exists($loginFailureLog)) {
    $logs .= "=== Logs de connexions échouées ===\n" . file_get_contents($loginFailureLog) . "\n\n";
}
if (file_exists($errorLog)) {
    $logs .= "=== Logs d'erreurs ===\n" . file_get_contents($errorLog) . "\n\n";
}

$rapport = analyserLogsAvecMistral($logs);

// Enregistrer le rapport dans un fichier
$rapportFile = $rapportDir . 'rapport_' . date('Y-m-d_H-i-s') . '.txt';
file_put_contents($rapportFile, $rapport);

// Rediriger vers le tableau de bord admin avec une notification
$_SESSION['notification'] = "Un nouveau rapport de logs a été généré.";
header('Location: /ligue1/admin_m1');
exit;
?>
