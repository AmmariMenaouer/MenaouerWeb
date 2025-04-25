<?php
// Configuration de base
$apiKey = "2a7f5c00449d0c51d8987f4ee9cede9f"; // Clé API OpenWeatherMap

// Gestion de la langue
$availableLangs = ['fr' => 'Français', 'en' => 'English'];
$lang = isset($_GET['lang']) && array_key_exists($_GET['lang'], $availableLangs) ? $_GET['lang'] : 'fr';
setcookie('lang', $lang, time() + (86400 * 30), "/"); // Cookie valable 30 jours

// Gestion du thème
$availableThemes = ['light', 'dark'];
$theme = isset($_GET['theme']) && in_array($_GET['theme'], $availableThemes) ? $_GET['theme'] : 'light';
setcookie('theme', $theme, time() + (86400 * 30), "/"); // Cookie valable 30 jours

$translations = [
    'fr' => [
        'title' => 'MétéoFrance',
        'search' => 'Rechercher une ville...',
        'search_button' => 'Chercher',
        'feels_like' => 'Ressenti',
        'humidity' => 'Humidité',
        'wind' => 'Vent',
        'sunrise' => 'Lever',
        'sunset' => 'Coucher',
        'error' => 'Erreur',
        'not_found' => 'Ville non trouvée',
        'home' => 'Accueil',
        'map' => 'Carte',
        'stats' => 'Statistiques',
        'contact' => 'Contact',
        'current_temp' => 'Température actuelle',
        'language' => 'Langue',
        'theme_light' => 'Mode clair',
        'theme_dark' => 'Mode sombre'
    ],
    'en' => [
        'title' => 'MétéoFrance',
        'search' => 'Search for a city...',
        'search_button' => 'Search',
        'feels_like' => 'Feels like',
        'humidity' => 'Humidity',
        'wind' => 'Wind',
        'sunrise' => 'Sunrise',
        'sunset' => 'Sunset',
        'error' => 'Error',
        'not_found' => 'City not found',
        'home' => 'Home',
        'map' => 'Map',
        'stats' => 'Statistics',
        'contact' => 'Contact',
        'current_temp' => 'Current temperature',
        'language' => 'Language',
        'theme_light' => 'Light mode',
        'theme_dark' => 'Dark mode'
    ]
];

$t = $translations[$lang] ?? $translations['fr']; // Fallback au français
?>