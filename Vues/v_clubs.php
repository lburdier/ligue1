<?php
include_once __DIR__ . '/../Models/GestionBDD.php';
include_once __DIR__ . '/../Models/GestionUtilisateur.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    header('Location: /ligue1/connexion');
    exit;
}

try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();
    $gestionUtilisateur = new GestionUtilisateur($cnx);

    $idUtilisateur = $_SESSION['user']['id'];
    $clubsSuivis = $gestionUtilisateur->getClubsSuivis($idUtilisateur);
    $clubsSuivisIds = array_column($clubsSuivis, 'id_club');
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des clubs suivis : " . $e->getMessage());
    $clubsSuivis = [];
    $clubsSuivisIds = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clubs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../menu.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Liste des clubs et leurs stades</h2>
    <a href="/ligue1/generate_report" class="btn btn-success mb-4">📄 Télécharger le rapport des clubs</a>

    <div class="row">
        <?php foreach ($tab as $club): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm p-3">
                    <img src="<?= !empty($club['image']) ? htmlspecialchars($club['image'], ENT_QUOTES, 'UTF-8') : '/ligue1/img/placeholder.jpg'; ?>"
                         alt="Image du club" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($club['nom_club']); ?></h5>
                        <h6 class="card-subtitle mb-2">Ligue : <?= htmlspecialchars($club['ligue_club']); ?></h6>
                        <p class="card-text">
                            🏟️ <strong><?= htmlspecialchars($club['nom_stade']); ?></strong><br>
                            📍 <?= htmlspecialchars($club['ville']); ?><br>
                            👥 Capacité : <?= number_format($club['capacite'], 0, ',', ' '); ?>
                        </p>
                        <div class="mt-auto">
                            <a href="/ligue1/club_details?id=<?= urlencode($club['id_club']); ?>" class="btn btn-primary mt-3">Voir Détails</a>
                            <div class="mt-3">
                                <?= in_array($club['id_club'], $clubsSuivisIds) ? '<span class="badge bg-success">Club suivi</span>' : '<span class="badge bg-secondary">Non suivi</span>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="mt-5">Tableau des clubs</h2>
    <table class="table table-dark table-striped mt-3">
        <thead>
            <tr>
                <th>Nom du Club</th>
                <th>Ville</th>
                <th>Ligue</th>
                <th>Suivre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tab as $club): ?>
                <tr>
                    <td><?= htmlspecialchars($club['nom_club']) ?></td>
                    <td><?= htmlspecialchars($club['ville']) ?></td>
                    <td><?= htmlspecialchars($club['ligue_club']) ?></td>
                    <td>
                        <form action="/ligue1/Controleur/c_suivre_club.php" method="POST">
                            <input type="hidden" name="id_club" value="<?= htmlspecialchars($club['id_club'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="checkbox" name="suivre"
                                   class="form-check-input"
                                   id="suivre-<?= $club['id_club'] ?>"
                                   onchange="this.form.submit();"
                                   <?= in_array($club['id_club'], $clubsSuivisIds) ? 'checked' : '' ?>>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>