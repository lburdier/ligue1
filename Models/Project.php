<?php
// models/Project.php

class Project {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllProjects() {
        try {
            $query = $this->db->prepare("SELECT * FROM projects ORDER BY date DESC");
            $query->execute();
            $projects = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Si aucune donnée n'est présente dans la base de données, vous pouvez retourner des données de simulation ou un tableau vide
            if (!$projects) {
                return [
                    [
                        'id' => 1,
                        'title' => 'E-commerce Platform',
                        'description' => 'Full-stack e-commerce solution built with PHP/MySQL',
                        'technologies' => ['PHP', 'MySQL', 'JavaScript', 'Bootstrap'],
                        'image' => 'ecommerce.jpg'
                    ],
                    [
                        'id' => 2,
                        'title' => 'Portfolio Website',
                        'description' => 'Personal portfolio showcasing web development projects',
                        'technologies' => ['HTML', 'CSS', 'JavaScript'],
                        'image' => 'portfolio.jpg'
                    ],
                    // Ajoutez d'autres projets pour la simulation
                ];
            }
            
            return $projects;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des projets : " . $e->getMessage());
            return []; // Retourne un tableau vide en cas d'erreur
        }
    }
}