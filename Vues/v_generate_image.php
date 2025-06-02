<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur d'Images</title>
    <link rel="stylesheet" href="/ligue1/style/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #121212;
            color: #f1f1f1;
            display: flex;
            justify-content: center;
            padding: 50px;
        }

        #generator {
            background: #1e1e1e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 100%;
        }

        input[type="text"] {
            padding: 10px;
            width: 80%;
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        button {
            background: #4fa3ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #82bfff;
        }

        #loader {
            margin-top: 20px;
            font-style: italic;
            color: #ccc;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .hidden {
            display: none;
        }

        #result img {
            max-width: 100%;
            margin-top: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-in-out;
        }

        #result.show img {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div id="generator" class="text-center">
        <h2>Génère ton image 🖼️</h2>
        <input type="text" id="prompt" placeholder="Décris ton image..." />
        <button id="generateBtn">Générer</button>
        
        <div id="loader" class="hidden">⏳ Génération en cours...</div>
        
        <div id="result" class="hidden">
            <img id="generatedImage" src="" alt="Image générée" />
        </div>
    </div>

    <script>
        document.getElementById('generateBtn').addEventListener('click', () => {
            const prompt = document.getElementById('prompt').value;
            const loader = document.getElementById('loader');
            const result = document.getElementById('result');
            const img = document.getElementById('generatedImage');

            if (!prompt.trim()) return alert("Écris quelque chose pour générer une image !");

            loader.classList.remove('hidden');
            result.classList.add('hidden');
            img.src = '';

            fetch('/ligue1/api/generate_image.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ titre: prompt })
            })
            .then(res => res.json())
            .then(data => {
                loader.classList.add('hidden');
                if (data.success) {
                    img.src = data.image_url;
                    result.classList.remove('hidden');
                    result.classList.add('show');
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(err => {
                loader.classList.add('hidden');
                alert("Erreur de communication : " + err.message);
            });
        });
    </script>
</body>
</html>
