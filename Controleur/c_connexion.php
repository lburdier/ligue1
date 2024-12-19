<?php

ob_start(); // Démarrer la mise en mémoire tampon de sortie

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Clé secrète reCAPTCHA v2
$recaptchaSecret = '6Leg3HUqAAAAAAT37ieKtLjParL2fEb9s4ALxfO3';

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
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'Un email valide est requis.';
    }
    if (empty($motdepasse)) {
        $erreurs[] = 'Le mot de passe est requis.';
    }

    if (empty($erreurs)) {
        if (!empty($recaptchaResponse)) {
            $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify';
            $response = file_get_contents($recaptchaURL . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
            $responseKeys = json_decode($response, true);

            if ($responseKeys["success"]) {
                $banniQuery = $cnx->prepare("SELECT banni_jusqu_a FROM banni_ips WHERE ip = :ip AND banni_jusqu_a > NOW()");
                $banniQuery->execute(['ip' => $userIP]);
                $banRecord = $banniQuery->fetch(PDO::FETCH_ASSOC);

                if ($banRecord) {
                    $banniJusquA = new DateTime($banRecord['banni_jusqu_a']);
                    $now = new DateTime();
                    $tempsRestantEnSecondes = $banniJusquA->getTimestamp() - $now->getTimestamp();
                    $_SESSION['temps_restant'] = $tempsRestantEnSecondes;
                    $erreurs[] = "Votre adresse IP est temporairement bannie. Veuillez réessayer dans <span id='decompte'></span>.";
                } else {
                    $stmt = $cnx->prepare("SELECT tentatives_connexion, derniere_tentative FROM utilisateur WHERE mail_uti = :email");
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch();

                    if ($user && $user['tentatives_connexion'] >= 5 && strtotime($user['derniere_tentative']) > strtotime('-15 minutes')) {
                        $tempsRestant = strtotime($user['derniere_tentative']) + (15 * 60) - time();
                        $erreurs[] = "Votre compte est temporairement verrouillé. Veuillez réessayer dans {$tempsRestant} secondes.";
                    } else {
                        try {
                            $utilisateur = $gestionUtilisateur->loginUser($email, $motdepasse);

                            if ($utilisateur) {
                                $_SESSION['user'] = [
                                    'id' => $utilisateur['id_uti'] ?? null,
                                    'prenom' => $utilisateur['prenom_uti'] ?? '',
                                    'nom' => $utilisateur['nom_uti'] ?? '',
                                    'email' => $utilisateur['mail_uti'] ?? '',
                                    'image' => $utilisateur['image_uti'] ?? '',
                                    'role' => $utilisateur['role_uti'] ?? 'utilisateur'
                                ];

                                $resetStmt = $cnx->prepare("UPDATE utilisateur SET tentatives_connexion = 0, derniere_tentative = NULL WHERE mail_uti = :email");
                                $resetStmt->execute(['email' => $email]);

                                logConnexion(true, $email);

                                if ($_SESSION['user']['role'] === 'admin') {
                                    echo '<script>window.location.href = "/admin-dashboard";</script>';
                                } else {
                                    echo '<script>window.location.href = "/ligue1";</script>';
                                }
                                exit;
                            } else {
                                $updateStmt = $cnx->prepare("UPDATE utilisateur SET tentatives_connexion = tentatives_connexion + 1, derniere_tentative = NOW() WHERE mail_uti = :email");
                                $updateStmt->execute(['email' => $email]);

                                logConnexion(false, $email);

                                $tentatives = $user['tentatives_connexion'] + 1;
                                if ($tentatives >= 5) {
                                    $checkBanStmt = $cnx->prepare("SELECT banni_jusqu_a FROM banni_ips WHERE ip = :ip");
                                    $checkBanStmt->execute(['ip' => $userIP]);
                                    $banRecord = $checkBanStmt->fetch(PDO::FETCH_ASSOC);

                                    if (!$banRecord || strtotime($banRecord['banni_jusqu_a']) <= time()) {
                                        $banStmt = $cnx->prepare("INSERT INTO banni_ips (ip, banni_jusqu_a) VALUES (:ip, NOW() + INTERVAL 15 MINUTE)");
                                        $banStmt->execute(['ip' => $userIP]);
                                        error_log("Adresse IP $userIP bannie pour 15 minutes.");
                                    }

                                    $erreurs[] = "Votre adresse IP est temporairement bannie.";
                                    $_SESSION['temps_restant'] = 900;
                                } else {
                                    $erreurs[] = 'Email ou mot de passe incorrect.';
                                }
                            }
                        } catch (Exception $e) {
                            $erreurs[] = 'Erreur lors de l\'authentification : ' . $e->getMessage();
                        }
                    }
                }
            } else {
                $erreurs[] = "Vérification reCAPTCHA échouée. Veuillez réessayer.";
            }
        } else {
            $erreurs[] = "Vérification reCAPTCHA manquante.";
        }
    }
}

// Inclure la vue après la logique complète pour éviter la sortie prématurée
$pageTitle = "Connexion - Ligue1";
include './Vues/v_connexion.php';

ob_end_flush(); // Libérer le tampon de sortie et envoyer la sortie au navigateur