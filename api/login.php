<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    include_once __DIR__ . '/../Models/GestionBDD.php';
    include_once __DIR__ . '/../Models/GestionUtilisateur.php';

    try {
        $gestionBDD = new GestionBDD("BD_ligue1");
        $cnx = $gestionBDD->connect();
        $gestionUtilisateur = new GestionUtilisateur($cnx);

        // Place la ligne suivante APRÈS l'initialisation de $gestionUtilisateur
        $user = $gestionUtilisateur->getUserByEmail($username);

        if ($user && isset($user['password_uti'])) {
            $dbPassword = $user['password_uti'];
            if (strpos($dbPassword, '$2y$') === 0) {
                $isValid = password_verify($password, $dbPassword);
            } else {
                $isValid = ($password === $dbPassword);
            }
            if ($isValid) {
                echo json_encode(['success' => true, 'user_id' => $user['id_uti']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}