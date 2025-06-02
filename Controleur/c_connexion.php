<?php

ob_start(); // Démarrer la mise en mémoire tampon de sortie

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Fonction de journalisation intégrée
function logConnexion($success, $email) {
    $logDir = realpath(__DIR__ . '/../logs/');

    if ($logDir === false) {
        error_log('Erreur : Le chemin du dossier des logs est invalide.');
        return;
    }

    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true)) {
            error_log('Erreur : Impossible de créer le dossier des logs.');
            return;
        } else {
            chmod($logDir, 0755);
        }
    }

    $logFile = $logDir . '/' . ($success ? 'login_success.log' : 'login_failure.log');

    if (!file_exists($logFile)) {
        if (file_put_contents($logFile, "=== Début du fichier de log ===" . PHP_EOL) === false) {
            error_log('Erreur : Impossible de créer le fichier de log ' . $logFile);
            return;
        }
    }

    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $status = $success ? 'SUCCESS' : 'FAILURE';

    $logMessage = "[$timestamp] - IP: $ip, Email: $email, Status: $status, User-Agent: $userAgent" . PHP_EOL;

    if (file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
        error_log('Erreur : Impossible d\'écrire dans le fichier de log ' . $logFile);
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$gestionBDD = new GestionBDD("BD_ligue1");
$erreurs = [];
$userIP = $_SERVER['REMOTE_ADDR'];

try {
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);
    $utilisateurs = $gestionUtilisateur->getListUtilisateurs();
} catch (Exception $e) {
    error_log('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
    $erreurs[] = 'Erreur lors de la récupération des utilisateurs.';
}

// Gestion des soumissions de formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $motdepasse = trim($_POST['motdepasse'] ?? '');
    $captchaValid = $_POST['captcha_valid'] ?? '0';

    // Vérification du CAPTCHA
    if ($captchaValid !== '1') {
        $erreurs[] = 'Vous devez valider correctement le CAPTCHA.';
    }

    // Validation des champs
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'Un email valide est requis.';
    }
    if (empty($motdepasse)) {
        $erreurs[] = 'Le mot de passe est requis.';
    }

    if (empty($erreurs)) {
        try {
            $utilisateur = $gestionUtilisateur->loginUser($email, $motdepasse);

            if ($utilisateur) {
                // Stocker les informations utilisateur dans la session
                $_SESSION['user'] = [
                    'id' => $utilisateur['id_uti'] ?? null,
                    'prenom' => $utilisateur['prenom_uti'] ?? '',
                    'nom' => $utilisateur['nom_uti'] ?? '',
                    'email' => $utilisateur['mail_uti'] ?? '',
                    'image' => $utilisateur['image_uti'] ?? '',
                    'role' => $utilisateur['role_uti'] ?? 'utilisateur'
                ];

                logConnexion(true, $email);

                // Redirection en fonction du rôle de l'utilisateur
                if ($_SESSION['user']['role'] === 'admin') {
                    header('Location: /ligue1/admin_dashboard');
                } else {
                    header('Location: /ligue1');
                }
                exit;
            } else {
                logConnexion(false, $email);
                $erreurs[] = 'Email ou mot de passe incorrect.';
            }
        } catch (Exception $e) {
            $erreurs[] = 'Erreur lors de l\'authentification : ' . $e->getMessage();
        }
    }
}

// Inclure la vue après la logique complète pour éviter la sortie prématurée
$pageTitle = "Connexion - Ligue1";
include './Vues/v_connexion.php';

ob_end_flush(); // Libérer le tampon de sortie et envoyer la sortie au navigateur