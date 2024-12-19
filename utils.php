<?php

function logConnexion($success, $email) {
    $logDir = realpath(__DIR__ . '/../logs/');

    // Vérifiez que le chemin est valide et affichable pour le débogage
    if ($logDir === false) {
        error_log('Erreur : Le chemin du dossier des logs est invalide.');
        echo 'Erreur : Le chemin du dossier des logs est invalide.'; // Affichage de débogage
        return;
    }

    error_log("Chemin du dossier de logs : " . $logDir);
    echo 'Chemin du dossier de logs : ' . $logDir . '<br>'; // Affichage de débogage

    // Vérifiez si le dossier existe, sinon essayez de le créer
    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true)) {
            error_log('Erreur : Impossible de créer le dossier des logs.');
            echo 'Erreur : Impossible de créer le dossier des logs.<br>'; // Affichage de débogage
            return;
        } else {
            chmod($logDir, 0755); // Assurez-vous que le dossier a les bonnes permissions
        }
    }

    // Définir le fichier de log
    $logFile = $logDir . '/' . ($success ? 'login_success.log' : 'login_failure.log');

    // Vérifiez si le fichier de log existe, sinon essayez de le créer
    if (!file_exists($logFile)) {
        if (file_put_contents($logFile, "=== Début du fichier de log ===" . PHP_EOL) === false) {
            error_log('Erreur : Impossible de créer le fichier de log ' . $logFile);
            echo 'Erreur : Impossible de créer le fichier de log ' . $logFile . '<br>'; // Affichage de débogage
            return;
        }
    }

    // Ajouter le message de log
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $status = $success ? 'SUCCESS' : 'FAILURE';

    $logMessage = "[$timestamp] - IP: $ip, Email: $email, Status: $status, User-Agent: $userAgent" . PHP_EOL;

    // Append au fichier log
    if (file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
        error_log('Erreur : Impossible d\'écrire dans le fichier de log ' . $logFile);
        echo 'Erreur : Impossible d\'écrire dans le fichier de log ' . $logFile . '<br>'; // Affichage de débogage
    } else {
        echo 'Log ajouté avec succès.<br>'; // Affichage de débogage
    }
}

?>