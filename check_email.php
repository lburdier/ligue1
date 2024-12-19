<?php
// Connexion à la base de données
include 'GestionBDD.php'; // Fichier de connexion à la base de données

// Vérifier si la requête est bien envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo 'check_mail trouver'; // Message de débogage

    // Vérifier si la connexion est bien établie
    if (!$conn) {
        die("Erreur de connexion à la base de données : " . mysqli_connect_error());
    }

    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Préparer la requête SQL pour vérifier si l'email existe déjà dans la colonne mail_uti
        $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE mail_uti = ?");
        
        if ($stmt === false) {
            die("Erreur lors de la préparation de la requête SQL : " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo 'exists'; // Si l'email existe
        } else {
            echo 'not_exists'; // Si l'email n'existe pas
        }
    }
} else {
    echo 'check_mail non trouvé'; // Si le fichier n'est pas atteint via la route correcte
}