<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['titre'], $data['description'], $data['categorie'], $data['imageBase64'], $data['userId'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

$titre = $data['titre'];
// La colonne dans la BDD s'appelle "contenu", on utilise donc "description" de l'API pour renseigner "contenu"
$contenu = $data['description'];
$categorie = $data['categorie'];
$imageBase64 = $data['imageBase64'];
$userId = intval($data['userId']);

// Assurer que le dossier "img" existe et le créer si nécessaire
$imgDir = __DIR__ . '/../img';
if (!is_dir($imgDir)) {
    if (!mkdir($imgDir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Impossible de créer le dossier des images']);
        exit;
    }
}

// Créer un nom d'image unique et enregistrer l'image
$imageName = "image_" . uniqid() . ".jpg";
$imagePath = $imgDir . '/' . $imageName;
if (!file_put_contents($imagePath, base64_decode($imageBase64))) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de l\'image']);
    exit;
}

// Chemin relatif à enregistrer en BDD (par exemple: "img/image_xxxx.jpg")
$imageRelativePath = "img/" . $imageName;

include_once __DIR__ . '/../Models/GestionBDD.php';
try {
    $gestionBDD = new GestionBDD("BD_ligue1");
    $cnx = $gestionBDD->connect();

    // Insertion dans la table 'article'. La colonne date_creation est gérée automatiquement.
    $stmt = $cnx->prepare("INSERT INTO article (titre, contenu, categorie, image, id_uti) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $contenu, $categorie, $imageRelativePath, $userId]);

    echo json_encode([
        'success' => true,
        'message' => 'Article créé avec succès',
        'image_url' => $imageRelativePath
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
