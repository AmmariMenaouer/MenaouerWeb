<?php
/**
 * @file config.php
 * @brief Configuration générale du site météo.
 * 
 * Ce fichier contient :
 * - La configuration des langues disponibles pour l'interface utilisateur
 * - Les thèmes disponibles (clair/sombre)
 * - Les clés API utilisées pour interroger les services météo externes
 * 
 * @author Groupe7
 * @version 1.0
 * @date 2025-04-16
 */

/**
 * @var array $availableLangs
 * Langues proposées pour l'affichage du site.
 * Les clés sont les codes de langue (ISO 639-1) et les valeurs les noms affichés.
 * 
 * @example ['fr' => 'Français', 'en' => 'English']
 */
$availableLangs = ['fr' => 'Français', 'en' => 'English'];

/**
 * @var array $availableThemes
 * Thèmes disponibles pour l'interface utilisateur.
 * 'light' pour le thème clair, 'dark' pour le thème sombre.
 * 
 * @example ['light', 'dark']
 */
$availableThemes = ['light', 'dark'];

/**
 * @var string $openWeatherApiKey
 * Clé API pour accéder au service météo OpenWeatherMap.
 * 
 * @see https://openweathermap.org/api
 */
$openWeatherApiKey = "2a7f5c00449d0c51d8987f4ee9cede9f";

/**
 * @var string $weatherApiKey
 * Clé API pour accéder au service WeatherAPI (utilisée en fallback).
 * 
 * @see https://www.weatherapi.com/
 */
$weatherApiKey = "25f40dedf6344f27bc0121852251104";
?>
