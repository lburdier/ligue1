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
$contenu = $request['contenu'] ?? '';

if (empty($titre) && empty($contenu)) {
    echo json_encode(['success' => false, 'message' => 'Le titre ou le contenu est requis.']);
    exit;
}

// Définir le prompt et le chemin de sortie
$prompt = $titre ?: $contenu;
$outputPath = __DIR__ . '/../img/generated_image_' . time() . '.png';

// Préparer la commande pour appeler le script Python
$command = escapeshellcmd("python c:/xampp/htdocs/ligue1/python/generate_image.py");
$input = json_encode(['prompt' => $prompt, 'output_path' => $outputPath]);

// Exécuter le script Python
$descriptorSpec = [
    0 => ["pipe", "r"], // stdin
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"], // stderr
];
$process = proc_open($command, $descriptorSpec, $pipes);

if (is_resource($process)) {
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $returnCode = proc_close($process);

    if ($returnCode === 0) {
        $result = json_decode($output, true);
        if ($result['success']) {
            echo json_encode(['success' => true, 'image_url' => '/ligue1/img/' . basename($outputPath)]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['error']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution du script Python.', 'details' => $error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Impossible d\'exécuter le script Python.']);
}
