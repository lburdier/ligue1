<?php

// Correct the Java program path and classpath
$javaProgramPath = "java -cp c:/xampp/htdocs/ligue1/java c:/xampp/htdocs/ligue1/java/ligue1/GenerateClubReport";
$javaClasspath = "c:/xampp/htdocs/ligue1/java;c:/xampp/htdocs/ligue1/java/lib/postgresql/postgresql-42.7.5.jar";
$javaMainClass = "ligue1.GenerateClubReport";

// Ensure the directory exists
$publicDir = __DIR__ . '/../public';
if (!is_dir($publicDir)) {
    if (!mkdir($publicDir, 0755, true)) {
        die("Erreur : Impossible de créer le répertoire public pour le rapport.");
    }
}

// Path to the report file
$outputFilePath = $publicDir . '/rapport.txt';

// Compile the Java program (optional, for debugging purposes)
exec("javac -d c:/xampp/htdocs/ligue1/java/out c:/xampp/htdocs/ligue1/java/db/DBConfig.java c:/xampp/htdocs/ligue1/java/db/DBConnection.java c:/xampp/htdocs/ligue1/java/ligue1/GenerateClubReport.java 2>&1", $compileOutput, $compileReturnCode);
if ($compileReturnCode !== 0) {
    echo "<p>Erreur : Impossible de compiler le programme Java.</p>";
    echo "<pre>Sortie de la compilation : " . htmlspecialchars(implode("\n", $compileOutput)) . "</pre>";
    exit;
}

// Execute the Java program with the corrected classpath and main class
$javaClasspath = "c:/xampp/htdocs/ligue1/java/out;c:/xampp/htdocs/ligue1/java/lib/postgresql/postgresql-42.7.5.jar";
$javaMainClass = "ligue1.GenerateClubReport";
$command = "java -cp \"$javaClasspath\" $javaMainClass";
exec($command . " 2>&1", $output, $returnCode);

if ($returnCode !== 0) {
    // Log detailed error information
    error_log("Erreur lors de l'exécution du programme Java. Code de retour : $returnCode. Sortie : " . implode("\n", $output));

    // Display detailed error message for debugging
    echo "<p>Erreur : Impossible de générer le rapport. Veuillez vérifier les logs pour plus de détails.</p>";
    echo "<pre>Sortie de la commande Java : " . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

// Debug: Check if the file exists
if (!file_exists($outputFilePath)) {
    die("Erreur : Le fichier du rapport est introuvable. Chemin testé : $outputFilePath");
}

// Serve the report file for download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="rapport.txt"');
readfile($outputFilePath);
exit;
?>
