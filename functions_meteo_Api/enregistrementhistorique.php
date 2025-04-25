<?php

/**
 * Récupère l’adresse IP réelle de l’utilisateur
 */
function getAdresseIP() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Retourne le chemin du fichier CSV d'historique basé sur l'adresse IP
 */
function getHistoriquePath() {
    $dossier = __DIR__ . '/../data/historiques_ip';

    // Création du dossier récursivement si nécessaire
    if (!is_dir($dossier) && !mkdir($dossier, 0777, true)) {
        error_log("❌ Impossible de créer le dossier $dossier");
        return null;
    }

    $ip = getAdresseIP(); // IP réelle de l'utilisateur
    $fichier = 'historique_' . str_replace([':', '.'], '_', $ip) . '.csv';

    return $dossier . '/' . $fichier;
}

/**
 * Enregistre une nouvelle recherche dans l'historique utilisateur
 * avec la météo en description (ex : "Pluie", "Ensoleillé")
 */
function enregistrerHistorique($ville, $lat = '', $lon = '', $meteo = 'Inconnue') {
    if (empty($ville)) return;

    $fichier = getHistoriquePath();
    if (empty($fichier)) return;

    $hist = lireHistorique();

    // Évite les doublons
    foreach ($hist as $item) {
        if (isset($item[0]) && strtolower(trim($item[0])) === strtolower(trim($ville))) {
            return;
        }
    }

    $date = date("Y-m-d H:i:s");
    $ligne = [$ville, $date, $lat, $lon, $meteo]; // Ajout de la météo

    $handle = @fopen($fichier, 'a');
    if ($handle) {
        fputcsv($handle, $ligne);
        fclose($handle);

        // Log facultatif
        $ip = getAdresseIP();
        file_put_contents(__DIR__ . '/../logs/debug_hist.txt', "✅ [$ip] Historique ajouté : $ville ($lat, $lon, $meteo) - $date\n", FILE_APPEND);
    } else {
        error_log("❌ Impossible d’écrire dans le fichier $fichier");
    }
}

/**
 * Lit l'historique depuis le fichier IP
 */
function lireHistorique() {
    $fichier = getHistoriquePath();
    if (empty($fichier) || !file_exists($fichier)) return [];

    $lignes = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map('str_getcsv', $lignes);
}

/**
 * Supprime une ville spécifique de l'historique
 */
function supprimerHistorique($villeASupprimer) {
    $fichier = getHistoriquePath();
    if (empty($fichier) || !file_exists($fichier)) return;

    $lignes = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nouvelles = array_filter($lignes, function($ligne) use ($villeASupprimer) {
        $ville = str_getcsv($ligne)[0] ?? '';
        return strtolower(trim($ville)) !== strtolower(trim($villeASupprimer));
    });

    if (!empty($nouvelles)) {
        file_put_contents($fichier, implode("\n", $nouvelles));
    } else {
        unlink($fichier); // fichier supprimé si vide
    }
}

/**
 * Supprime totalement le fichier d'historique lié à l'IP
 */
function supprimerToutHistorique() {
    $fichier = getHistoriquePath();
    if (!empty($fichier) && file_exists($fichier)) {
        unlink($fichier);
    }
}
