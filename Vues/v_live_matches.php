<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchs en Direct</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/ligue1/style/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../menu.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Matchs en Direct</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($matches)): ?>
        <div class="row g-4">
            <?php foreach ($matches as $match): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($match['HomeTeam'] . ' vs ' . $match['AwayTeam']); ?></h5>
                            <p class="card-text"><strong>Score :</strong> <?= htmlspecialchars($match['HomeTeamScore'] ?? 'N/A') . ' - ' . htmlspecialchars($match['AwayTeamScore'] ?? 'N/A'); ?></p>
                            <p class="card-text"><strong>Statut :</strong> <?= htmlspecialchars($match['Status']); ?></p>
                            <p class="card-text"><strong>Date :</strong> <?= htmlspecialchars($match['Day']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun match en direct disponible pour le moment. Voici des vidéos récentes de la chaîne Sponitor :</p>
        <div class="row g-4" id="youtube-videos"></div>
    <?php endif; ?>
</div>

<script>
    async function fetchSportsVideos() {
        const apiKey = 'AIzaSyDRrzgs7oIlxk1Zi8gc_aHMMFJilijUdgU'; // Your YouTube API key
        const query = 'sports highlights'; // Query to fetch sports-related videos
        const maxResults = 6;

        const url = `https://www.googleapis.com/youtube/v3/search?key=${apiKey}&part=snippet&type=video&order=relevance&maxResults=${maxResults}&q=${encodeURIComponent(query)}`;

        const container = document.getElementById('youtube-videos');
        container.innerHTML = '';

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (!data.items || data.items.length === 0) {
                container.innerHTML = '<p>Aucune vidéo disponible pour le moment.</p>';
                return;
            }

            data.items.forEach(video => {
                const videoId = video.id.videoId;
                const videoTitle = video.snippet.title;

                const videoHtml = `
                    <div class="col-lg-6 mb-4">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/${videoId}" title="${videoTitle}" allowfullscreen></iframe>
                        </div>
                        <p class="mt-2 text-white">${videoTitle}</p>
                    </div>
                `;
                container.innerHTML += videoHtml;
            });
        } catch (error) {
            console.error('Erreur lors de la récupération des vidéos YouTube :', error);
            container.innerHTML = '<p>Impossible de charger les vidéos.</p>';
        }
    }

    // Call the function on page load
    fetchSportsVideos();
</script>
</body>
</html>
