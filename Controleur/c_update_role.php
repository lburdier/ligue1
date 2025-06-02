<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    echo "<p>Erreur : Vous devez être connecté pour effectuer cette action.</p>";
    exit;
}

// Connexion à la base de données
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
} catch (Exception $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    echo "<p>Erreur : Impossible de se connecter à la base de données.</p>";
    exit;
}

// Mettre à jour le rôle de l'utilisateur
$gestionUtilisateur = new GestionUtilisateur($cnx);
$role = $_POST['role_createur'] ?? '';

if ($gestionUtilisateur->updateUserRole($_SESSION['user']['id'], $role)) {
    $_SESSION['user']['role'] = $role; // Mettre à jour le rôle dans la session
    echo "<p>Votre rôle a été mis à jour avec succès.</p>";
} else {
    echo "<p>Erreur lors de la mise à jour de votre rôle.</p>";
}

// Rediriger vers le profil
header("Location: /ligue1/Vues/v_profil.php");
exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mettre à jour un rôle</title>
</head>
<body>
    <h1>Mettre à jour un rôle</h1>
    <form method="post" action="c_update_role.php">
        <label for="role_id">ID du rôle:</label>
        <input type="text" id="role_id" name="role_id" required><br><br>
        <label for="role_name">Nom du rôle:</label>
        <input type="text" id="role_name" name="role_name" required><br><br>
        <input type="submit" value="Mettre à jour">
    </form>
</body>
</html>
