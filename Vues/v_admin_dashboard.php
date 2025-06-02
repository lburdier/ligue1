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

$pageTitle = "Tableau de Bord Admin";
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
            /* Dark mode styles for the table and text */
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

            body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
                background-color: #2a2a2a;
            }

            body.dark-mode .table-striped tbody tr:nth-of-type(even) {
                background-color: #1e1e1e;
            }

            body.dark-mode h1,
            body.dark-mode h2,
            body.dark-mode h3 {
                color: #e0e0e0;
            }

            body.dark-mode .alert {
                background-color: #2b2b2b;
                color: #e0e0e0;
                border-color: #444;
            }
        </style>
    </head>
    <body>
        <div class="container mt-5">
            <h1>Tableau de bord Administrateur</h1>

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
                        echo "<li><a href='/ligue1/logs/rapport/$filename' target='_blank'>$filename</a></li>";
                    endforeach;
                else:
                    echo "<li>Aucun rapport disponible.</li>";
                endif;
                ?>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['id_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['mail_uti']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['date_inscription']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>