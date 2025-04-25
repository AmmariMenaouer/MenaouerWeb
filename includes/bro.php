<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === Traductions ===
$translations = [
    'fr' => [
        'title' => 'WeatherCY',
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
        'theme_dark' => 'Mode sombre',
        'forecast' => 'Prévisions',
        'history' => 'Historique',
        'about' => 'À propos',
        'back_home' => "Retour à l'accueil",
        'forecast_7d' => "Prévisions sur 7 jours",
        'forecast_by_hour' => "Prévisions heure par heure pour aujourd'hui",
        'search_city' => "Recherchez une ville pour consulter la météo actuelle et les prévisions heure par heure.",
        'card_map' => "Carte Interactive",
        'card_map_desc' => "Cliquez sur une région pour consulter directement la météo locale avec prévisions détaillées.",
        'card_search' => "Recherche Intelligente",
        'card_search_desc' => "Tapez le nom d’une ville pour afficher immédiatement la météo actuelle et les prévisions heure par heure.",
        'card_stats' => "Analyse Météo",
        'card_stats_desc' => "Visualisez l’évolution de la température, humidité, pression ou précipitations sous forme de graphique interactif.",
        'search_placeholder' => "Ex : Paris, Marseille...",
        'button_search' => "Rechercher",
        'current_weather' => "Météo actuelle à",
        'temp' => "Température",
        'min_max' => "Min / Max",
        'visibility' => "Visibilité",
        'department' => "Département",
        'region' => "Région",
        'hourly_analysis' => "Analyse météo horaire",
        'temp_metric' => "Temp",
        'humidity_metric' => "Humidité",
        'pressure_metric' => "Pression",
        'precip_metric' => "Pluie",
        'last_update' => "Dernière mise à jour",
        'see_forecast' => "Voir les prévisions sur 7 jours",
        'cookie_title' => "Ce site utilise des cookies",
        'cookie_text' => "Nous utilisons des cookies pour améliorer votre expérience utilisateur.",
        'accept' => "Accepter",
        'refuse' => "Refuser",
        'history_title' => "Historique des Recherches",
        'top_cities' => "Villes les plus recherchées",
        'history_table' => "Détails des recherches",
        'th_city' => "Ville",
        'th_date' => "Date",
        'th_temp' => "Température",
        'th_humidity' => "Humidité",
        'th_wind' => "Vent",
        'th_pressure' => "Pression",
        'th_description' => "Description",
        'title_apropos' => 'À propos du projet',
        'section_objectif' => 'Objectif du site',
        'text_objectif' => 'Ce site a été développé dans le cadre d’un projet académique visant à créer une application web de prévisions météorologiques modernes, interactives et précises.',
        'section_fonctionnalites' => 'Fonctionnalités principales',
        'section_technologies' => 'Technologies utilisées',
        'section_developpeurs' => 'Développeurs',
        'texte_developpeurs' => 'Ce projet a été développé par Ammari Menaouer et Omar Hachani (L2 MIPI), encadrés par le Prof. Lemaire Marc.',
        'merci' => 'Merci pour votre visite 👋'
    ],
    'en' => [
        'title' => 'WeatherCY',
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
        'theme_dark' => 'Dark mode',
        'forecast' => 'Forecast',
        'history' => 'History',
        'about' => 'About',
        'back_home' => "Back to homepage",
        'forecast_7d' => "7-Day Forecast",
        'forecast_by_hour' => "Hourly Forecast for Today",
        'search_city' => "Search for a city to view current weather and hourly forecast.",
        'card_map' => "Interactive Map",
        'card_map_desc' => "Click on a region to view detailed local weather and forecast.",
        'card_search' => "Smart Search",
        'card_search_desc' => "Type a city name to instantly view weather and hourly forecasts.",
        'card_stats' => "Weather Analysis",
        'card_stats_desc' => "View temperature, humidity, pressure or precipitation trends with an interactive chart.",
        'search_placeholder' => "Ex: Paris, Marseille...",
        'button_search' => "Search",
        'current_weather' => "Current weather in",
        'temp' => "Temperature",
        'min_max' => "Min / Max",
        'visibility' => "Visibility",
        'department' => "Department",
        'region' => "Region",
        'hourly_analysis' => "Hourly Weather Analysis",
        'temp_metric' => "Temp",
        'humidity_metric' => "Humidity",
        'pressure_metric' => "Pressure",
        'precip_metric' => "Rain",
        'last_update' => "Last update",
        'see_forecast' => "See 7-day forecast",
        'cookie_title' => "This site uses cookies",
        'cookie_text' => "We use cookies to enhance your browsing experience.",
        'accept' => "Accept",
        'refuse' => "Refuse",
        'history_title' => "Search History",
        'top_cities' => "Most searched cities",
        'history_table' => "Search details",
        'th_city' => "City",
        'th_date' => "Date",
        'th_temp' => "Temperature",
        'th_humidity' => "Humidity",
        'th_wind' => "Wind",
        'th_pressure' => "Pressure",
        'th_description' => "Description",
        'title_apropos' => 'About the Project',
        'section_objectif' => 'Site Objective',
        'text_objectif' => 'This site was developed as part of an academic project to build a modern, interactive and accurate weather forecasting web app.',
        'section_fonctionnalites' => 'Main Features',
        'section_technologies' => 'Technologies Used',
        'section_developpeurs' => 'Developers',
        'texte_developpeurs' => 'Developed by Ammari Menaouer and Omar Hachani (L2 MIPI), under the guidance of Prof. Marc Lemaire.',
        'merci' => 'Thanks for visiting 👋'
    ]
];

// === Gestion de la langue via ?lang=fr|en ===
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $translations)) {
    $_SESSION['lang'] = $_GET['lang'];
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    parse_str($_SERVER['QUERY_STRING'], $params);
    unset($params['lang']);
    $newQuery = http_build_query($params);
    if (!empty($newQuery)) {
        $url .= '?' . $newQuery;
    }
    header("Location: $url");
    exit;
}

$lang = $_SESSION['lang'] ?? 'fr';
$t = $translations[$lang] ?? $translations['fr'];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $t['title'] ?></title>
  <link rel="icon" href="../images/favicon.png" type="image/png" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<header class="entete">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navbar">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <img src="../images/logo.jpeg" alt="Logo" style="height: 40px; border-radius: 50%;">
        <span><?= $t['title'] ?></span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menuPrincipal">
        <ul class="navbar-nav me-auto   mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> <?= $t['home'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="previsions.php"><i class="fas fa-cloud-sun-rain me-1"></i> <?= $t['forecast'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="historique.php"><i class="fas fa-history me-1"></i> <?= $t['history'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="historiqueglobal.php"><i class="fas fa-database me-1"></i> Historique Global</a></li>
          <li class="nav-item"><a class="nav-link" href="apropos.php"><i class="fas fa-info-circle me-1"></i> <?= $t['about'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fas fa-envelope me-1"></i> <?= $t['contact'] ?></a></li>
        </ul>

        <!-- Sélecteur de langue -->
        <form class="d-flex align-items-center me-3" method="get" action="">
          <select name="lang" class="form-select" onchange="this.form.submit()">
            <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>🇫🇷 Français</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
          </select>
        </form>

        <!-- Bouton thème -->
        <button id="bouton-theme" class="btn btn-light mode-switch" title="<?= $t['theme_dark'] ?>/<?= $t['theme_light'] ?>">
          <i id="icone-theme" class="fas fa-moon"></i>
        </button>
      </div>
    </div>
  </nav>
</header>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const bouton = document.getElementById('bouton-theme');
    const icone = document.getElementById('icone-theme');

    let theme = localStorage.getItem("theme");

    if (!theme) {
      theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      localStorage.setItem("theme", theme);
    }

    // Appliquer le thème sauvegardé
    if (theme === "dark") {
      body.classList.add("dark-mode");
      icone.classList.remove("fa-moon");
      icone.classList.add("fa-sun");
    }

    // Toggle lors du clic
    bouton.addEventListener("click", () => {
      const isDark = body.classList.toggle("dark-mode");
      localStorage.setItem("theme", isDark ? "dark" : "light");

      // Changement d'icône
      if (isDark) {
        icone.classList.remove("fa-moon");
        icone.classList.add("fa-sun");
      } else {
        icone.classList.remove("fa-sun");
        icone.classList.add("fa-moon");
      }
    });

    // Effet au scroll pour navbar
    window.addEventListener("scroll", function () {
      const nav = document.getElementById("navbar");
      nav.classList.toggle("scrolled", window.scrollY > 10);
    });
  });
</script>

