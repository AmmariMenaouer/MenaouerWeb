<?php

// ============================================================================
// INITIALISATION DES VARIABLES DE SESSION SI ELLES N'EXISTENT PAS
// Ces variables servent à suivre les statistiques globales et par ville
// ============================================================================
$_SESSION['nombre_requetes'] ??= 0;               // Nombre total de requêtes météo
$_SESSION['somme_temperatures'] ??= 0;            // Somme des températures reçues
$_SESSION['nombre_temperatures'] ??= 0;           // Nombre total de températures valides enregistrées
$_SESSION['villes_consultees'] ??= [];            // Historique détaillé avec horodatage (ville|datetime)
$_SESSION['historique_recherches'] ??= [];        // Liste simple des villes recherchées récemment

/**
 * Met à jour les statistiques météo pour une ville spécifique.
 *
 * @param string $ville Nom de la ville consultée.
 * @param array $meteo Données météo reçues au format standardisé.
 */
function mettreAJourStatistiques($ville, $meteo) {
    $_SESSION['nombre_requetes']++; // Incrémentation globale

    // Extraction sécurisée des données météo
    $temp = $meteo['main']['temp'] ?? null;
    $humidity = $meteo['main']['humidity'] ?? null;
    $wind = $meteo['wind']['speed'] ?? null;
    $pressure = $meteo['main']['pressure'] ?? null;
    $weather = $meteo['weather'][0]['description'] ?? null;

    // Mise à jour des statistiques globales si température valide
    if ($temp !== null) {
        $_SESSION['somme_temperatures'] += $temp;
        $_SESSION['nombre_temperatures']++;
    }

    // Clé unique composée de la ville et de la date/heure
    $key = $ville . '|' . date('Y-m-d H:i:s');

    // Ajout dans l’historique détaillé
    $_SESSION['villes_consultees'][$key] = compact('temp', 'humidity', 'wind', 'pressure', 'weather');

    // Limitation mémoire : garder uniquement les 50 dernières entrées
    if (count($_SESSION['villes_consultees']) > 50) {
        array_shift($_SESSION['villes_consultees']);
    }

    // Historique simple : ajout si non déjà présent
    if (!in_array($ville, $_SESSION['historique_recherches'])) {
        $_SESSION['historique_recherches'][] = $ville;

        // Limitation : garder les 20 dernières villes
        if (count($_SESSION['historique_recherches']) > 20) {
            array_shift($_SESSION['historique_recherches']);
        }
    }
}

/**
 * Calcule la température moyenne globale sur toutes les requêtes enregistrées.
 *
 * @return float Température moyenne arrondie à 1 décimale, ou 0 si aucune donnée.
 */
function calculerTemperatureMoyenne() {
    return ($_SESSION['nombre_temperatures'] ?? 0) > 0
        ? round($_SESSION['somme_temperatures'] / $_SESSION['nombre_temperatures'], 1)
        : 0;
}
/**
 * Récupère les statistiques météo spécifiques pour une ville donnée.
 *
 * @param string $ville Nom de la ville ciblée.
 * @return array Tableau associatif contenant :
 *     - count : nombre total d'enregistrements pour cette ville,
 *     - avg_temp : température moyenne,
 *     - avg_humidity : humidité moyenne,
 *     - avg_wind : vitesse moyenne du vent,
 *     - history : liste complète des relevés pour cette ville.
 */
function obtenirStatistiquesVille($ville) {
    $stats = [
        'count' => 0,
        'avg_temp' => 0,
        'avg_humidity' => 0,
        'avg_wind' => 0,
        'history' => []
    ];

    $sumTemp = $sumHum = $sumWind = 0;

    foreach ($_SESSION['villes_consultees'] as $key => $entry) {
        // Filtrage des entrées de cette ville uniquement
        if (strpos($key, $ville . '|') === 0) {
            $stats['history'][] = $entry;

            if ($entry['temp'] !== null) {
                $sumTemp += $entry['temp'];
                $sumHum += $entry['humidity'];
                $sumWind += $entry['wind'];
                $stats['count']++;
            }
        }
    }

    if ($stats['count'] > 0) {
        $stats['avg_temp'] = round($sumTemp / $stats['count'], 1);
        $stats['avg_humidity'] = round($sumHum / $stats['count'], 1);
        $stats['avg_wind'] = round($sumWind / $stats['count'], 1);
    }

    return $stats;
}
