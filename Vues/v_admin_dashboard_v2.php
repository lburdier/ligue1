<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the 'user' and 'role' keys exist in the session
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    // Prevent output before header redirection
    ob_clean();
    header('Location: /ligue1/index.php');
    exit;
}

$pageTitle = "Tableau de Bord Admin - Version 2";
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="/ligue1/style/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
        <style>
            /* Add custom styles for version 2 if needed */
            body.dark-mode .table {
                background-color: #1e1e1e;
                color: #e0e0e0;
                border: 1px solid #444;
            }

            body.dark-mode .table th,
            body.dark-mode .table td {
                border-color: #555;
                color: #e0e0e0;
            }

            body.dark-mode .table th {
                background-color: #2b2b2b;
            }

            body.dark-mode h1,
            body.dark-mode h2,
            body.dark-mode h3 {
                color: #e0e0e0;
            }
        </style>
    </head>
    <body>
        <div class="container mt-5">
            <h1>Tableau de bord Administrateur - Version 2</h1>

            <?php if (!empty($_SESSION['notification'])): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($_SESSION['notification']); ?>
                    <?php unset($_SESSION['notification']); ?>
                </div>
            <?php endif; ?>

            <h3>Rapports de Logs</h3>
            <ul>
                <?php
                $rapportDir = __DIR__ . '/../logs/rapport/';
                $rapports = glob($rapportDir . '*.txt');
                if (!empty($rapports)):
                    foreach ($rapports as $rapport):
                        $filename = basename($rapport);
                        ?>
                        <li>
                            <a href="/ligue1/logs/rapport/<?php echo htmlspecialchars($filename); ?>" target="_blank"><?php echo htmlspecialchars($filename); ?></a>
                            <form method="POST" action="/ligue1/Controleur/c_delete_rapport.php" style="display: inline;">
                                <input type="hidden" name="rapport_filename" value="<?php echo htmlspecialchars($filename); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Aucun rapport disponible.</li>
                <?php endif; ?>
            </ul>

            <a href="/ligue1/Controleur/c_analyse_logs.php" class="btn btn-primary">Analyser les Logs</a>

            <h2>Liste des utilisateurs</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date d'inscription</th>
                        <th>Dernière activité</th>
                        <th>Statut</th>
                        <th>Banni</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['id_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['mail_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['date_inscription']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['derniere_activite'] ?? 'Inconnue'); ?></td>
                            <td>
                                <?php if (!empty($utilisateur['banni_jusqu_a']) && new DateTime($utilisateur['banni_jusqu_a']) > new DateTime()): ?>
                                    <span class="text-danger">Banni</span>
                                    <br>
                                    <small>
                                        Jusqu'à : <?php echo htmlspecialchars($utilisateur['banni_jusqu_a']); ?>
                                    </small>
                                <?php else: ?>
                                    <span class="text-success">Actif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($utilisateur['banni_jusqu_a']) && new DateTime($utilisateur['banni_jusqu_a']) > new DateTime()): ?>
                                    Oui
                                <?php else: ?>
                                    Non
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_user_id" value="<?php echo htmlspecialchars($utilisateur['id_uti']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="ban_user_id" value="<?php echo htmlspecialchars($utilisateur['id_uti']); ?>">
                                    <input type="number" name="ban_duration" placeholder="Durée (jours)" min="1" class="form-control-sm" required>
                                    <button type="submit" class="btn btn-warning btn-sm">Bannir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>