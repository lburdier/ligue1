<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio</title>
    <link rel="stylesheet" href="style.css"> <!-- Assurez-vous que le chemin est correct -->
    <style>
        :root {
            --dark-bg: #0a0a0a;
            --text-color: #ffffff;
            --accent-color: #3498db;
            --card-bg: rgba(255, 255, 255, 0.08);
            --shadow-color: rgba(0, 0, 0, 0.2);
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .hero {
            text-align: center;
            padding: 2rem 0;
            border-bottom: 1px solid var(--card-bg);
        }

        .projects {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .project-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px var(--shadow-color);
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px var(--shadow-color);
        }

        .project-card img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: transform 0.3s ease-in-out;
        }

        .project-card img:hover {
            transform: scale(1.05);
        }

        .project-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }

        .project-card p {
            font-size: 1rem;
            color: #d1d1d1;
            margin-bottom: 1rem;
        }

        .tech-tag {
            background-color: var(--accent-color);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 0.4rem;
            display: inline-block;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <main class="container">
        <section class="hero">
            <h2>Développeur Web Full Stack</h2>
            <p>Spécialisé en PHP, JavaScript et architectures modernes</p>
        </section>
        
        <section class="projects">
            <?php foreach($projects as $project): ?>
                <article class="project-card">
                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <h3><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="technologies">
                        <?php foreach($project['technologies'] as $tech): ?>
                            <span class="tech-tag"><?= htmlspecialchars($tech, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
