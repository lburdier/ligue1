<?php

ob_start(); // Démarre le tampon de sortie
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionClubs.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

$gestionBDD = new GestionBDD("BD_ligue1");

// Initialiser les erreurs
$erreurs = [];

// Récupérer la liste des clubs
try {
    $cnx = $gestionBDD->connect();
    $gc = new GestionClubs($cnx);
    $tab = $gc->getListClub();
} catch (Exception $e) {
    $erreurs[] = 'Erreur lors de la récupération des clubs : ' . $e->getMessage();
}

// Gestion des soumissions de formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $id_club = $_POST['id_club'] ?? 0;
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';
    $motdepasse_confirmation = $_POST['motdepasse_confirmation'] ?? '';  // Confirmation du mot de passe
    $sexe = $_POST['sexe'] ?? '';

    // ### Validation en PHP basée sur les `patterns` HTML ###
    // Validation du prénom : lettres uniquement, max 10 caractères
    if (empty($prenom) || !preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{1,10}$/', $prenom)) {
        $erreurs[] = 'Le prénom ne doit contenir que des lettres, des espaces ou des tirets, et ne pas dépasser 10 caractères.';
    }

    // Validation du nom : lettres uniquement, max 10 caractères
    if (empty($nom) || !preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{1,10}$/', $nom)) {
        $erreurs[] = 'Le nom ne doit contenir que des lettres, des espaces ou des tirets, et ne pas dépasser 10 caractères.';
    }

    // Validation de l'email : doit être valide avec .com ou .fr
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|fr)$/', $email)) {
        $erreurs[] = 'Veuillez entrer une adresse email valide se terminant par .com ou .fr.';
    }

    // Validation des mots de passe : vérification de la correspondance des deux saisies
    if ($motdepasse !== $motdepasse_confirmation) {
        $erreurs[] = 'Les mots de passe ne correspondent pas.';
    }

    // Validation du mot de passe : min 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial
    if (empty($motdepasse) || !preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $motdepasse)) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.';
    }

    // Validation du sexe : doit être Homme, Femme, ou Autre
    if (empty($sexe) || !in_array($sexe, ['Homme', 'Femme', 'Autre'])) {
        $erreurs[] = 'Veuillez sélectionner un sexe valide.';
    }

    // Valider que le club a été sélectionné
    $gestionUtilisateur = new GestionUtilisateur($cnx);
    if (empty($id_club) || !$gestionUtilisateur->isValidClub($id_club)) {
        $erreurs[] = 'Veuillez sélectionner un club valide.';
    }

    // Validation de l'image (chemin ou URL correcte)
    if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
        $erreurs[] = 'Veuillez fournir une URL valide pour l\'image de profil.';
    }

    // Vérifier si l'email existe déjà avec la méthode `emailExists`
    if ($gestionUtilisateur->emailExists($email)) {
        $errorEmail = 'Cet email est déjà utilisé.';
        $emailExistant = true; // Indicateur que l'email existe
    } else {
        $emailExistant = false;
    }

    // Enregistrement dans la base de données si pas d'erreurs
    if (empty($erreurs) && !$emailExistant) {
        try {
            $isInserted = $gestionUtilisateur->insertUser($id_club, $nom, $prenom, $sexe, $motdepasse, $imagePath, $email);

            if ($isInserted) {
                $_SESSION['nom'] = $nom; // Stocker le nom dans la session
                echo "<script>window.location.href='/ligue1/confirmation';</script>";
                exit; // Terminer le script après redirection
            } else {
                $erreurs[] = 'Erreur lors de l\'insertion de l\'utilisateur.';
            }
        } catch (Exception $e) {
            $erreurs[] = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
        }
    }
}

// Inclure la vue pour afficher le formulaire d'inscription
$pageTitle = "Inscription - Ligue1";
include './Vues/v_inscription.php';

ob_end_flush(); // Envoie le contenu du tampon et désactive le tampon de sortie