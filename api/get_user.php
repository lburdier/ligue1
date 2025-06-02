<?php
header('Content-Type: application/json');
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}
$id = intval($_GET['id']);
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);
    $user = $gestionUtilisateur->getUserById($id);
    if ($user) {
        echo json_encode(['id' => $user['id_uti'], 'name' => $user['nom_uti']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
