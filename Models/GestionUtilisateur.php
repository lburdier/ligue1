<?php

class GestionUtilisateur {

    private $cnx;

    public function __construct($connexion) {
        $this->cnx = $connexion;
    }

    // Récupérer la liste des utilisateurs
    public function getListUtilisateurs(): array {
        $res = $this->cnx->query("SELECT * FROM utilisateur");
        $tabResultat = [];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $tabResultat[] = $row;
        }
        return $tabResultat;
    }

    // Vérifier si le club existe
    public function isValidClub($id_club): bool {
        $sql = "SELECT COUNT(*) FROM club WHERE id_club = ?";
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute([$id_club]);
        return $stmt->fetchColumn() > 0; // Retourne vrai si le club existe
    }

    // Vérifier si l'avatar est valide
    public function isValidAvatar($imagePath): bool {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE image_uti = ?";
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute([$imagePath]);
        return $stmt->fetchColumn() > 0; // Retourne vrai si l'avatar existe dans la base de données
    }

    // Insérer un nouvel utilisateur sans hachage du mot de passe
    public function insertUser($id_club, $nom, $prenom, $sexe, $motdepasse, $imagePath, $email): bool {
        try {
            $sql = "INSERT INTO utilisateur (id_club, nom_uti, prenom_uti, sexe_uti, password_uti, date_inscription, image_uti, mail_uti) 
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([
                $id_club,
                $nom,
                $prenom,
                $sexe,
                $motdepasse, // Pas de hachage du mot de passe
                $imagePath,
                $email
            ]);
            return true; // Retourner vrai si l'insertion réussit
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'insertion de l\'utilisateur : ' . $e->getMessage());
            return false; // Retourner faux en cas d'erreur
        }
    }

    // Récupérer un utilisateur par son email
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE mail_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Log des données récupérées pour le débogage
            if ($result) {
                error_log('Données utilisateur récupérées : ' . json_encode($result));
            } else {
                error_log('Aucun utilisateur trouvé avec l\'email : ' . $email);
            }

            return $result; // Retourne null si aucun utilisateur n'est trouvé
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
            return null; // Retourner null en cas d'erreur
        }
    }

    // Mettre à jour l'image de l'utilisateur
    public function updateImageUser($id_uti, $imagePath): bool {
        try {
            $sql = "UPDATE utilisateur SET image_uti = ? WHERE id_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$imagePath, $id_uti]);
            return true; // Retourner vrai si la mise à jour réussit
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de l\'image de l\'utilisateur : ' . $e->getMessage());
            return false; // Retourner faux en cas d'erreur
        }
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers(): array {
        try {
            $sql = "SELECT mail_uti, password_uti, nom_uti FROM utilisateur"; // Ajoutez les colonnes nécessaires
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les utilisateurs
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
            return []; // Retourne un tableau vide en cas d'erreur
        }
    }

    // Authentifier un utilisateur
    public function loginUser($email, $motdepasse) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE mail_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            // Comparer le mot de passe en clair avec celui stocké
            if ($utilisateur && $motdepasse === $utilisateur['password_uti']) {
                return $utilisateur; // Retourne l'utilisateur si la vérification est réussie
            } else {
                return null; // Retourne null si les informations ne correspondent pas
            }
        } catch (PDOException $e) {
            error_log('Erreur lors de la connexion de l\'utilisateur : ' . $e->getMessage());
            return null; // Retourner null en cas d'erreur
        }
    }

    public function getUserByIdAndName($id, $nom) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE id_uti = ? AND nom_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$id, $nom]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne les informations de l'utilisateur
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
            return null; // Retourner null en cas d'erreur
        }
    }

    public function emailExists($email) {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM utilisateur WHERE mail_uti = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetchColumn() > 0; // Retourne vrai si l'email existe
    }

    public function enregistrerActivite($id) {
        $sql = "UPDATE utilisateur SET last_connexion = NOW() WHERE id_uti = ?";
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute([$id]);
    }

    public function deleteUserById($id) {
        try {
            error_log('Début de la suppression de l\'utilisateur avec l\'ID : ' . $id); // Ajoutez ceci
            // Vérifiez d'abord si l'utilisateur existe
            $sqlCheck = "SELECT COUNT(*) FROM utilisateur WHERE id_uti = ?";
            $stmtCheck = $this->cnx->prepare($sqlCheck);
            $stmtCheck->execute([$id]);
            $exists = $stmtCheck->fetchColumn();

            if ($exists == 0) {
                error_log('Aucun utilisateur trouvé avec l\'ID : ' . $id);
                return false; // Retourner faux si l'utilisateur n'existe pas
            }

            // Requête de suppression
            $sql = "DELETE FROM utilisateur WHERE id_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $result = $stmt->execute([$id]); // Retourne true si la suppression réussit

            if ($result) {
                error_log('Utilisateur avec l\'ID ' . $id . ' a été supprimé.');
            } else {
                error_log('Échec de la suppression de l\'utilisateur avec l\'ID : ' . $id);
            }

            return $result; // Retourner le résultat de l'exécution
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
            return false; // Retourner faux en cas d'erreur
        }
    }

    // Incrémenter le compteur de tentatives de connexion échouées et mettre à jour la dernière tentative
    public function incrementLoginAttempts($email) {
        try {
            $sql = "UPDATE utilisateur 
                    SET tentatives_connexion = tentatives_connexion + 1, derniere_tentative = NOW() 
                    WHERE mail_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$email]);
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'incrémentation des tentatives de connexion : ' . $e->getMessage());
        }
    }

    // Réinitialiser le compteur de tentatives de connexion en cas de connexion réussie
    public function resetLoginAttempts($email) {
        try {
            $sql = "UPDATE utilisateur 
                    SET tentatives_connexion = 0, derniere_tntative = NULL 
                    WHERE mail_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$email]);
        } catch (PDOException $e) {
            error_log('Erreur lors de la réinitialisation des tentatives de connexion : ' . $e->getMessage());
        }
    }

    public function afficherBtnModificationById($article, $utilisateurConnecte) {
        // Vérifier que l'utilisateur connecté est bien défini et que l'article est valide
        if ($article && isset($utilisateurConnecte['id_uti'])) {
            // Vérifier si l'utilisateur connecté est le propriétaire de l'article
            if ($article->getIdUtilisateur() === $utilisateurConnecte['id_uti']) {
                echo '<a href="/ligue1/modifier_article.php?id=' . htmlspecialchars($article->getId(), ENT_QUOTES, 'UTF-8') . '" class="edit-button">Modifier l\'article</a>';
            }
        } else {
            error_log('Erreur : utilisateur connecté ou article non défini.');
        }
    }

    public function getIdUtilisateurConnecte() {
        if (isset($_SESSION['user']['id'])) {
            return $_SESSION['user']['id']; // Retourne l'ID de l'utilisateur connecté
        }
        return null; // Retourne null si aucun utilisateur n'est connecté
    }

    // Récupérer les tentatives de connexion échouées et la dernière tentative pour un utilisateur
    public function getLoginAttempts($email) {
        try {
            $sql = "SELECT tentatives_connexion, derniere_tentative FROM utilisateur WHERE mail_uti = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne les données ou false si l'utilisateur n'existe pas
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des tentatives de connexion : ' . $e->getMessage());
            return null;
        }
    }

    // Bannir une adresse IP en cas de dépassement de tentatives échouées
    public function banIP($ip) {
        try {
            $sql = "INSERT INTO banni_ips (ip, banni_jusqu_a) VALUES (?, NOW() + INTERVAL '15 minutes')";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$ip]);
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'ajout de l\'adresse IP à la liste des IPs bannies : ' . $e->getMessage());
        }
    }

    // Vérifier si une IP est bannie
    public function isIPBanned($ip) {
        try {
            $sql = "SELECT banni_jusqu_a FROM banni_ips WHERE ip = ?";
            $stmt = $this->cnx->prepare($sql);
            $stmt->execute([$ip]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $banniJusquA = new DateTime($result['banni_jusqu_a']);
                $now = new DateTime();

                if ($banniJusquA > $now) {
                    // Calculer le temps restant en minutes
                    $interval = $now->diff($banniJusquA);
                    return [
                        'banni' => true,
                        'temps_restant' => $interval
                    ];
                }
            }
            return ['banni' => false];
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'IP bannie $ip : " . $e->getMessage());
            return ['banni' => false];
        }
    }

    // Vérifier si l'utilisateur est banni via son IP
    public function isUserBannedByIP($ip) {
        try {
            $sql = "SELECT banni_jusqu_a FROM banni_ips WHERE ip = :ip AND banni_jusqu_a > NOW()";
            $stmt = $this->cnx->prepare($sql);
            $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['banni_jusqu_a']; // Retourne la date de fin du bannissement
            }
            return null; // Pas de bannissement actif
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du bannissement par IP : " . $e->getMessage());
            return null;
        }
    }

    public function updateUserRole($id, $role) {
        try {
            $query = $this->cnx->prepare("UPDATE utilisateur SET role_uti = :role WHERE id_uti = :id");
            $query->execute(['role' => $role, 'id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du rôle de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function banUserById($userId, $durationInDays) {
        try {
            $query = "UPDATE utilisateur SET banni_jusqu_a = DATE_ADD(NOW(), INTERVAL :duration DAY) WHERE id_uti = :id";
            $stmt = $this->cnx->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':duration', $durationInDays, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors du bannissement de l'utilisateur : " . $e->getMessage());
        }
    }

    public function suivreClub($idUtilisateur, $idClub) {
        try {
            $query = "INSERT INTO utilisateur_club (id_utilisateur, id_club, etat) 
                      VALUES (:id_utilisateur, :id_club, TRUE)
                      ON CONFLICT (id_utilisateur, id_club) DO UPDATE SET etat = TRUE";
            $stmt = $this->cnx->prepare($query);
            $stmt->bindParam(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindParam(':id_club', $idClub, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout du suivi du club : " . $e->getMessage());
            return false;
        }
    }

    public function getClubsSuivis($idUtilisateur) {
        try {
            $query = "SELECT id_club FROM suivi WHERE id_utilisateur = ?";
            $stmt = $this->cnx->prepare($query);
            $stmt->execute([$idUtilisateur]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des clubs suivis : " . $e->getMessage());
            return [];
        }
    }

    public function nePlusSuivreClub($idUtilisateur, $idClub) {
        try {
            $query = "UPDATE utilisateur_club SET etat = FALSE 
                      WHERE id_utilisateur = :id_utilisateur AND id_club = :id_club";
            $stmt = $this->cnx->prepare($query);
            $stmt->bindParam(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindParam(':id_club', $idClub, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du suivi du club : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a user is already following a club.
     *
     * @param int $idUtilisateur
     * @param int $idClub
     * @return bool
     */
    public function isFollowingClub($idUtilisateur, $idClub) {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM v_club WHERE id_utilisateur = :idUtilisateur AND id_club = :idClub");
        $stmt->execute(['idUtilisateur' => $idUtilisateur, 'idClub' => $idClub]);
        return $stmt->fetchColumn() > 0;
    }
}
