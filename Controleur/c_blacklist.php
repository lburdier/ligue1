<?php

/**
 * Vérifie si un texte contient des mots interdits définis dans les fichiers de config/update/.
 *
 * @param string $texte Le texte à vérifier.
 * @return string|null Retourne le premier mot interdit trouvé ou null si aucun mot n'est trouvé.
 */
function verifierMotsInterdits($texte) {
    // Répertoire contenant les fichiers de mots interdits
    $dossierMotsInterdits = __DIR__ . '/../config/update/';

    // Normaliser le texte (convertir en minuscule)
    $texteLower = strtolower($texte);

    // Vérifier l'existence du dossier
    if (!is_dir($dossierMotsInterdits)) {
        error_log("Le dossier des mots interdits est introuvable : $dossierMotsInterdits");
        return null;
    }

    // Parcourir tous les fichiers .txt dans le dossier
    $fichiers = glob($dossierMotsInterdits . '*.txt');
    foreach ($fichiers as $fichier) {
        if (!is_readable($fichier)) {
            error_log("Impossible de lire le fichier des mots interdits : $fichier");
            continue;
        }

        // Lire les mots interdits depuis le fichier
        $motsInterdits = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($motsInterdits as $mot) {
            $mot = strtolower(trim($mot)); // Normaliser le mot
            if (!empty($mot) && strpos($texteLower, $mot) !== false) {
                // Mot interdit trouvé
                error_log("Mot interdit détecté dans le fichier $fichier : $mot");
                return $mot;
            }
        }
    }

    // Aucun mot interdit trouvé
    return null;
}
?>