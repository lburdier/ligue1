<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Tableau de Bord Admin";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="style/style.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container mt-5">
            <h1>Bienvenue sur le tableau de bord admin</h1>
            <p>Ceci est une page cachée, accessible uniquement aux administrateurs.</p>

            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <h2>Liste des utilisateurs</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date d'inscription</th>
                        <th>Dernière connexion</th>
                        <th>Statut (Inactif)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentDate = new DateTime();
                    foreach ($utilisateurs as $utilisateur):
                        $dateInscription = new DateTime($utilisateur['date_inscription']);
                        $lastConnexion = isset($utilisateur['last_connexion']) ? new DateTime($utilisateur['last_connexion']) : null;
                        $inactiveStatus = false;
                        if (is_null($lastConnexion) && $currentDate->diff($dateInscription)->days >= 90) {
                            $inactiveStatus = true;
                        } elseif (!is_null($lastConnexion) && $currentDate->diff($lastConnexion)->days >= 90) {
                            $inactiveStatus = true;
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['id_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['mail_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['date_inscription']); ?></td>
                            <td><?php echo is_null($lastConnexion) ? 'Jamais connecté' : htmlspecialchars(date('Y-m-d H:i:s', strtotime($utilisateur['last_connexion']))); ?></td>
                            <td>
                                <?php echo $inactiveStatus ? '<span class="text-danger">Inactif</span>' : '<span class="text-success">Actif</span>'; ?>
                            </td>
                            <td>
                                <?php if ($inactiveStatus): ?>
                                    <form action="/ligue1/delete_profil" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur inactif ?');">
                                        <input type="hidden" name="delete_user_id" value="<?php echo htmlspecialchars($utilisateur['id_uti']); ?>">
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Supprimer</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Section pour afficher les logs -->
            <h2>Logs de connexion</h2>
            <div class="logs-section">
                <?php
                $logDir = __DIR__ . '/../logs/';
                $logFiles = ['login_success.log', 'login_failure.log'];

                foreach ($logFiles as $logFile) {
                    $filePath = $logDir . $logFile;
                    if (file_exists($filePath)) {
                        echo "<h3>" . htmlspecialchars($logFile) . "</h3>";
                        echo "<pre class='log-content'>";
                        echo htmlspecialchars(file_get_contents($filePath));
                        echo "</pre>";
                    } else {
                        echo "<p>Le fichier " . htmlspecialchars($logFile) . " n'existe pas.</p>";
                    }
                }
                ?>
            </div>
        </div>
    </body>
</html>