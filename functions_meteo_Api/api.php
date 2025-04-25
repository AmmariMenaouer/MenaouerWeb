<?php
/**
 * @file api.php
 * @brief Fonctions de communication avec les APIs météo (OpenWeatherMap & WeatherAPI).
 * 
 * Ce fichier fournit les fonctions pour :
 * - Obtenir les coordonnées géographiques d'une ville
 * - Récupérer la météo actuelle
 * - Obtenir les prévisions à 5 jours (OpenWeatherMap)
 * - Obtenir les prévisions à 7 jours (WeatherAPI)
 * 
 */

require_once 'config.php';  ///< Contient les clés API et paramètres globaux
require_once 'utils.php';   ///< Contient appelApi() pour faire des requêtes HTTP

/**
 * @function obtenirCoordonnees
 * @brief Récupère les coordonnées géographiques (latitude, longitude) d'une ville.
 * 
 * Utilise le service de géocodage d'OpenWeatherMap pour retrouver les coordonnées GPS
 * ainsi que le pays d'une ville donnée.
 * 
 * @param string $ville Nom de la ville à localiser
 * @return array|null Tableau associatif avec 'lat', 'lon', 'nom', 'pays' ou null si erreur
 * 
 * @see https://openweathermap.org/api/geocoding-api
 */
function obtenirCoordonnees($ville) {
    global $openWeatherApiKey;

    $url = "https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($ville) . "&limit=1&appid=$openWeatherApiKey";
    $data = appelApi($url);

    return (!empty($data[0]['lat']) && !empty($data[0]['lon'])) ? [
        'lat' => $data[0]['lat'],
        'lon' => $data[0]['lon'],
        'nom' => $data[0]['name'] ?? $ville,
        'pays' => $data[0]['country'] ?? ''
    ] : null;
}

/**
 * @function obtenirMeteoActuelle
 * @brief Récupère la météo actuelle à partir des coordonnées GPS.
 * 
 * Utilise OpenWeatherMap comme source principale et WeatherAPI comme fallback
 * si la première API échoue.
 * 
 * @param float $lat Latitude du lieu
 * @param float $lon Longitude du lieu
 * @return array Tableau associatif avec :
 *  - 'source' => Nom de l'API utilisée ('openweathermap', 'weatherapi' ou 'aucune')
 *  - 'data' => Données météo brutes retournées par l'API (ou null)
 * 
 * @see https://openweathermap.org/current
 * @see https://www.weatherapi.com/docs/
 */
function obtenirMeteoActuelle($lat, $lon) {
    global $openWeatherApiKey, $weatherApiKey;

    $url1 = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=metric&lang=fr&appid=$openWeatherApiKey";
    $data1 = appelApi($url1);

    if ($data1 && isset($data1['main'])) {
        return ['source' => 'openweathermap', 'data' => $data1];
    }

    $url2 = "https://api.weatherapi.com/v1/current.json?key=$weatherApiKey&q=$lat,$lon&lang=fr";
    $data2 = appelApi($url2);

    if ($data2 && isset($data2['current'])) {
        return ['source' => 'weatherapi', 'data' => $data2];
    }

    return ['source' => 'aucune', 'data' => null];
}

/**
 * @function obtenirPrevisions5Jours
 * @brief Récupère les prévisions météo sur 5 jours (par tranches de 3h).
 * 
 * Utilise l'API OpenWeatherMap pour obtenir les prévisions.
 * 
 * @param float $lat Latitude du lieu
 * @param float $lon Longitude du lieu
 * @return array|null Données météo brutes (ou null si erreur)
 * 
 * @see https://openweathermap.org/forecast5
 */
function obtenirPrevisions5Jours($lat, $lon) {
    global $openWeatherApiKey;

    $url = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=fr&appid=$openWeatherApiKey";
    return appelApi($url);
}

/**
 * @function obtenirPrevisions7Jours
 * @brief Récupère les prévisions météo sur 7 jours à partir de WeatherAPI.
 * 
 * L'API retourne une prévision quotidienne incluant température, humidité, vent, etc.
 * 
 * @param float $lat Latitude du lieu
 * @param float $lon Longitude du lieu
 * @return array|null Données JSON retournées par l’API ou null si erreur
 * 
 * @see https://www.weatherapi.com/docs/
 */
function obtenirPrevisions7Jours($lat, $lon) {
    global $weatherApiKey;

    $url = "https://api.weatherapi.com/v1/forecast.json?key=$weatherApiKey&q=$lat,$lon&days=7&lang=fr";
    return appelApi($url);
}
