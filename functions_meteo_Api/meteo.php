<?php
/**
 * Point d'entrée pour les traitements AJAX du site météo.
 * 
 * Ce fichier gère :
 * 1. La récupération des coordonnées à partir du nom d'une ville.
 * 2. L'obtention des données météo actuelles et des prévisions.
 * 3. La mise à jour des statistiques météo.
 * 4. L'enregistrement dans un fichier CSV.
 * 5. L'accès aux statistiques globales et par ville.
 * 
 * @author Ammari Menaouer
 * @author Omar Hachani 
 */

require_once 'api.php';              ///< Contient les fonctions pour interroger les APIs météo
require_once 'statistiques.php';     ///< Gère le suivi statistique
require_once 'enregistrement.php';   ///< Gère l'enregistrement dans un fichier CSV

// ============================================================================
// 1. TRAITEMENT AJAX POUR OBTENIR LES COORDONNÉES D'UNE VILLE
// ============================================================================
/**
 * @param string $_GET['ville_recherche_ajax'] Nom de la ville à géocoder
 * @return JSON Coordonnées géographiques de la ville (ou tableau vide si erreur)
 */
if (isset($_GET['ville_recherche_ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(obtenirCoordonnees($_GET['ville_recherche_ajax']) ?: []);
    exit;
}

// ============================================================================
// 2. TRAITEMENT D'UNE REQUÊTE AVEC LATITUDE ET LONGITUDE
// ============================================================================
/**
 * @param float $_GET['lat'] Latitude
 * @param float $_GET['lon'] Longitude
 * @param string $_GET['nom'] (optionnel) Nom de la localisation
 * @return JSON Contenant : météo actuelle, prévisions 5j, prévisions 7j, source
 * 
 * @see obtenirMeteoActuelle()
 * @see obtenirPrevisions5Jours()
 * @see obtenirPrevisions7Jours()
 * @see mettreAJourStatistiques()
 * @see enregistrerRechercheCSV()
 */
if (isset($_GET['lat'], $_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $nom = $_GET['nom'] ?? 'Coordonnées';

    $meteo = obtenirMeteoActuelle($lat, $lon);
    $previsions5 = obtenirPrevisions5Jours($lat, $lon);
    $previsions7 = obtenirPrevisions7Jours($lat, $lon);

    if ($meteo['data']) {
        if ($meteo['source'] === 'weatherapi') {
            $current = $meteo['data']['current'];

            $main = [
                'temp' => $current['temp_c'],
                'humidity' => $current['humidity'],
                'pressure' => $current['pressure_mb']
            ];
            $wind = [
                'speed' => $current['wind_kph'] / 3.6
            ];
            $weather = [[
                'description' => $current['condition']['text']
            ]];

            mettreAJourStatistiques($nom, [
                'main' => $main,
                'wind' => $wind,
                'weather' => $weather
            ]);
        } else {
            mettreAJourStatistiques($nom, $meteo['data']);
        }
    }

    enregistrerRechercheCSV($nom, $lat, $lon, $previsions7);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'source' => $meteo['source'],
        'meteo' => $meteo['data'],
        'previsions5' => $previsions5,
        'previsions7' => $previsions7
    ]);
    exit;
}

// ============================================================================
// 3. REQUÊTE POUR LES STATISTIQUES GLOBALES
// ============================================================================
/**
 * @param bool $_GET['stats_globales'] Présence de ce paramètre déclenche la réponse
 * @return JSON Contenant nombre total de requêtes, température moyenne et historique
 */
if (isset($_GET['stats_globales'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'nombre_requetes' => $_SESSION['nombre_requetes'],
        'temperature_moyenne' => calculerTemperatureMoyenne(),
        'villes_consultees' => array_values($_SESSION['historique_recherches'])
    ]);
    exit;
}

// ============================================================================
// 4. REQUÊTE POUR LES STATISTIQUES D'UNE VILLE
// ============================================================================
/**
 * @param string $_GET['stats_ville'] Nom de la ville pour laquelle on veut les stats
 * @return JSON Données statistiques sur la ville (températures, requêtes, etc.)
 * 
 * @see obtenirStatistiquesVille()
 */
if (isset($_GET['stats_ville']) && !empty($_GET['stats_ville'])) {
    header('Content-Type: application/json');
    echo json_encode(obtenirStatistiquesVille($_GET['stats_ville']));
    exit;
}
