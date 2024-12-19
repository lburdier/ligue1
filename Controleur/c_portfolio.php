<?php

ob_start(); // Démarrer la mise en mémoire tampon de sortie

include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/Project.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$gestionBDD = new GestionBDD("BD_ligue1"); // Remplacez par le nom de votre base de données
$erreurs = [];

try {
    $cnx = $gestionBDD->connect();
    $projectModel = new Project($cnx);
    $projects = $projectModel->getAllProjects();
} catch (Exception $e) {
    error_log('Erreur lors de la récupération des projets : ' . $e->getMessage());
    $erreurs[] = 'Erreur lors de la récupération des projets.';
}

// Gestion des requêtes POST pour des interactions éventuelles (ex. : ajout de projet)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = $_POST['technologies'] ?? [];
    $image = $_FILES['image'] ?? null;

    if (empty($title) || empty($description) || empty($technologies)) {
        $erreurs[] = 'Tous les champs obligatoires doivent être remplis.';
    }

    if (empty($erreurs)) {
        try {
            $imagePath = null;

            if ($image && $image['error'] === UPLOAD_ERR_OK) {
                $imagePath = $projectModel->uploadImage($image);
            }

            if ($projectModel->addProject($title, $description, $technologies, $imagePath)) {
                echo '<script>window.location.href = "/portfolio";</script>';
                exit;
            } else {
                $erreurs[] = 'Erreur lors de l\'ajout du projet.';
            }
        } catch (Exception $e) {
            $erreurs[] = 'Erreur lors de l\'ajout du projet : ' . $e->getMessage();
        }
    }
}

// Inclure la vue après la logique complète
$pageTitle = "Portfolio - Ligue1";
include __DIR__ . '/../Vues/v_portfolio.php'; // Correction du chemin

ob_end_flush(); // Libérer le tampon de sortie et envoyer la sortie au navigateur
