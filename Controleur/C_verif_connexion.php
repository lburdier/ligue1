<?php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Utilisateur.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

try {
    // Connexion à la base de données
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => "Une erreur interne est survenue. Veuillez réessayer plus tard."
    ]);
    exit;
}

// Vérifier si un utilisateur est connecté
if (empty($_SESSION['user']['id']) || empty($_SESSION['user']['prenom']) || empty($_SESSION['user']['nom'])) {
    error_log("Erreur : Informations utilisateur manquantes dans la session. Session actuelle : " . print_r($_SESSION, true));
    echo json_encode([
        'status' => 'error',
        'message' => "Vous devez être connecté pour effectuer cette action."
    ]);
    exit;
}

try {
    // Vérifier les informations de l'utilisateur connecté dans la base de données
    $utilisateur = $gestionUtilisateur->getUserByIdAndName($_SESSION['user']['id'], $_SESSION['user']['nom']);
    if (!$utilisateur) {
        error_log("Erreur : Utilisateur connecté introuvable dans la base de données. ID : {$_SESSION['user']['id']}, Nom : {$_SESSION['user']['nom']}");
        echo json_encode([
            'status' => 'error',
            'message' => "Votre session est invalide. Veuillez vous reconnecter."
        ]);
        exit;
    } else {
        // Stocker le rôle utilisateur dans la session
        $_SESSION['user']['role'] = $utilisateur['role_uti'];
    }
} catch (Exception $e) {
    error_log("Erreur lors de la vérification de l'utilisateur connecté : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => "Une erreur interne est survenue. Veuillez réessayer plus tard."
    ]);
    exit;
}

// Retourner les informations utilisateur
return [
    'id' => $utilisateur['id_uti'],
    'prenom' => $utilisateur['prenom_uti'],
    'nom' => $utilisateur['nom_uti'],
    'role' => $utilisateur['role_uti']
];
?>