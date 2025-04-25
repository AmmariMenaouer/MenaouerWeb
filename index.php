<?php
session_start();

include_once 'includes/lang.php';
require_once 'functions_meteo_Api/config.php';
require_once 'functions_meteo_Api/utils.php'; // Doit contenir getUserID()
require_once 'functions_meteo_Api/api.php';
require_once 'functions_meteo_Api/statistiques.php';
require_once 'functions_meteo_Api/enregistrement.php';
require_once 'functions_meteo_Api/enregistrementhistorique.php'; // Historique utilisateur

// Consentement aux cookies
$consentCookies = isset($_COOKIE['consentement']) && $_COOKIE['consentement'] === 'oui';

// Détection de la ville à afficher
if (isset($_GET['ville']) && !empty($_GET['ville'])) {
    $ville = htmlspecialchars($_GET['ville'], ENT_QUOTES, 'UTF-8');
    if ($consentCookies) {
        setcookie('ville', $ville, time() + (7 * 24 * 60 * 60), '/');
    }
} else {
    $ville = $consentCookies && isset($_COOKIE['ville']) ? $_COOKIE['ville'] : 'Paris';
}

// Initialisation
$meteo = $previsions = $coord = null;

// Récupération des coordonnées de la ville
$coord = obtenirCoordonnees($ville);
if ($coord && isset($coord['lat'], $coord['lon'])) {
    $lat = $coord['lat'];
    $lon = $coord['lon'];
    $nom = $coord['nom']; // Nom normalisé

    // Récupération météo actuelle et prévisions
    $meteo = obtenirMeteoActuelle($lat, $lon);
    $previsions = obtenirPrevisions7Jours($lat, $lon);

    // Si les prévisions sont incomplètes, on force un format minimal
    if (!$previsions || !isset($previsions['forecast'])) {
        $previsions = ['forecast' => ['forecastday' => []]];
        file_put_contents(__DIR__ . '/data/debug_index.txt', "[AVERTISSEMENT]  Prévisions manquantes pour $nom, enregistrement partiel forcé\n", FILE_APPEND);
    }

    if ($meteo && isset($meteo['data']) && $meteo['data']) {
        // Mise à jour des statistiques
        if ($meteo['source'] === 'weatherapi') {
            $current = $meteo['data']['current'];
            $main = [
                'temp' => $current['temp_c'],
                'humidity' => $current['humidity'],
                'pressure' => $current['pressure_mb']
            ];
            $wind = ['speed' => $current['wind_kph'] / 3.6];
            $weather = [['description' => $current['condition']['text']]];
            mettreAJourStatistiques($nom, ['main' => $main, 'wind' => $wind, 'weather' => $weather]);
        } else {
            mettreAJourStatistiques($nom, $meteo['data']);
        }
    }

    // Enregistrement CSV global (même sans météo complète)
    file_put_contents(__DIR__ . '/data/debug_index.txt', "[DEBUG] 📥 Appel à enregistrerRechercheCSV($nom)\n", FILE_APPEND);
    enregistrerRechercheCSV($nom, $lat, $lon, $previsions);

    // Historique utilisateur spécifique (avec identifiant cookie+IP)
    enregistrerHistorique($nom, $lat, $lon);

    // Debug utilisateur
    $debugFile = __DIR__ . '/data/debug_index.txt';
    if (function_exists('getUserID')) {
        $userID = getUserID();
        $log = "[" . date("Y-m-d H:i:s") . "] ✅ Historique enregistré pour $userID → $nom ($lat, $lon)\n";
        file_put_contents($debugFile, $log, FILE_APPEND);

        $histFile = getHistoriquePath();
        if (!file_exists($histFile)) {
            file_put_contents($debugFile, "[ERREUR] Fichier historique non trouvé pour $userID\n", FILE_APPEND);
        }
    } else {
        file_put_contents($debugFile, "[" . date("Y-m-d H:i:s") . "] ⚠️ Fonction getUserID() absente\n", FILE_APPEND);
    }
}
?>

<?php include 'includes/head.inc.php';
include 'includes/header.inc.php'; ?>




<main class="container py-5 ">
    <h1 class="text-center " style="color: white;"><?= $t['welcome_title'] ?></h1>
    <p class="text-center text-muted  " style="color: white; ">
    <?= $t['welcome_text'] ?>
    </p>
    
    <!-- BLOCS D'ACCUEIL INFORMATIFS -->
    <section class="info-blocs mb-5">
        <div class="row g-4 text-center justify-content-center">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm p-4 border-0">
                    <i class="fas fa-map-marked-alt fa-2x   text-primary"></i>
                    <h4 class=" "><?= $t['card_map'] ?></h4>
                    <p class="text-muted"><?= $t['card_map_desc'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm p-4 border-0">
                    <i class="fas fa-search-location fa-2x   text-success"></i>
                    <h4 class=" "><?= $t['card_search'] ?></h4>
                    <p class="text-muted"><?= $t['card_search_desc'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm p-4 border-0">
                    <i class="fas fa-chart-line fa-2x   text-warning"></i>
                    <h4 class=" "><?= $t['card_stats'] ?></h4>
                    <p class="text-muted"><?= $t['card_stats_desc'] ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- FORMULAIRE -->
    <section class="mb-5 ">
        <form method="GET" action="index.php" class="row justify-content-center g-3">
            <div class="col-md-6">
                <input type="text" name="ville" class="form-control form-control-lg text-center"
                       placeholder="Ex : Paris, Marseille..." value="<?= htmlspecialchars($ville, ENT_QUOTES, 'UTF-8') ?>" required="required" />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </form>
    </section>

    <?php if (isset($coord) && $coord): ?>
        <?php
        $lat = $coord['lat'];
        $lon = $coord['lon'];
        $nom = $coord['nom'];

        // Infos régionales
        $departement = 'Inconnu';
        $region = 'Inconnue';
        $infos = @file_get_contents("https://geo.api.gouv.fr/communes?nom=" . urlencode($ville) . "&fields=departement,region&format=json&limit=1&boost=population");
        $infos = json_decode($infos, true);
        if (is_array($infos) && isset($infos[0])) {
            $departement = $infos[0]['departement']['nom'] ?? 'Inconnu';
            $region = $infos[0]['region']['nom'] ?? 'Inconnue';
        }

        $meteo = obtenirMeteoActuelle($lat, $lon);
        $previsions = obtenirPrevisions7Jours($lat, $lon);
        ?>

        <!-- MÉTÉO ACTUELLE -->
        <section class="mb-5">
            <div class="card shadow p-4">
                <h2 class="text-center  "> <?= $t['current_weather'] ?> <strong><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?></strong></h2>

                <?php if (isset($meteo['data']) && $meteo['data']): ?>
                    <?php
                    $source = $meteo['source'];
                    $data = $meteo['data'];

                    if ($source === 'weatherapi') {
                        $current = $data['current'];
                        $desc = $current['condition']['text'];
                        $temp = $current['temp_c'];
                        $vent = round($current['wind_kph'] / 3.6, 1);
                        $humid = $current['humidity'];
                        $pression = $current['pressure_mb'];
                        $visibility = $current['vis_km'];
                        $temp_min = $data['forecast']['forecastday'][0]['day']['mintemp_c'];
                        $temp_max = $data['forecast']['forecastday'][0]['day']['maxtemp_c'];
                        $sunrise = $data['forecast']['forecastday'][0]['astro']['sunrise'];
                        $sunset = $data['forecast']['forecastday'][0]['astro']['sunset'];
                        $iconUrl = "https:" . $current['condition']['icon'];
                    } else {
                        $current = $data;
                        $desc = $current['weather'][0]['description'];
                        $temp = $current['main']['temp'];
                        $vent = $current['wind']['speed'];
                        $humid = $current['main']['humidity'];
                        $pression = $current['main']['pressure'];
                        $visibility = $current['visibility'] / 1000;
                        $temp_min = $current['main']['temp_min'];
                        $temp_max = $current['main']['temp_max'];
                        $sunrise = date("H\h i", $current['sys']['sunrise']);
                        $sunset = date("H\h i", $current['sys']['sunset']);
                        $code = $current['weather'][0]['icon'];
                        $iconUrl = "https://openweathermap.org/img/wn/{$code}@2x.png";
                    }
                    ?>

                    <div class="row text-center align-items-center">
                        <div class="col-md-3">
                            <img src="<?= htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>" style="width: 100px;" />
                            <p class="fs-5 mt-2"><?= ucfirst(htmlspecialchars($desc, ENT_QUOTES, 'UTF-8')) ?></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>🌡️ <?= $t['temp'] ?> :</strong> <?= htmlspecialchars($temp, ENT_QUOTES, 'UTF-8') ?> °C</p>
                            <p><strong>↕️ Min / Max :</strong> <?= htmlspecialchars($temp_min, ENT_QUOTES, 'UTF-8') ?> °C / <?= htmlspecialchars($temp_max, ENT_QUOTES, 'UTF-8') ?> °C</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>💧 <?= $t['humidity'] ?> :</strong> <?= htmlspecialchars($humid, ENT_QUOTES, 'UTF-8') ?>%</p>
                            <p><strong>🌬️ <?= $t['wind'] ?>:</strong> <?= htmlspecialchars($vent, ENT_QUOTES, 'UTF-8') ?> km/h</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>👁️ <?= $t['visibility'] ?> :</strong> <?= htmlspecialchars($visibility, ENT_QUOTES, 'UTF-8') ?> km</p>
                            <p><strong>📍 <?= $t['department'] ?> :</strong> <?= htmlspecialchars($departement, ENT_QUOTES, 'UTF-8') ?><br /><strong>Région :</strong> <?= htmlspecialchars($region, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>

                    <div class="row mt-4 text-center">
                        <div class="col-md-6">
                            <p><strong><?= $t['sunrise'] ?> :</strong> <?= htmlspecialchars($sunrise, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><?= $t['sunset'] ?> :</strong> <?= htmlspecialchars($sunset, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-danger text-center">Impossible de récupérer la météo.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- PRÉVISIONS HEURE PAR HEURE -->
        <?php
        $heureAujourdHui = [];
        if (isset($previsions['forecast']['forecastday'][0]['hour'])) {
            $heureAujourdHui = $previsions['forecast']['forecastday'][0]['hour'];
        }
        ?>

        <?php if ($heureAujourdHui): ?>
            <section>
                <div class="card shadow p-4">
                    <h2 class="text-center  "><?= $t['forecast_by_hour'] ?></h2>

                    <div class="scroll-wrapper">
                        <button onclick="scrollLeft()" class="btn-scroll">⬅️</button>

                        <div class="scroller" id="scroller">
                            <?php foreach ($heureAujourdHui as $heure): ?>
                                <?php
                                preg_match('/(\d+)\.png$/', $heure['condition']['icon'], $matches);
                                $codeApi = $matches[1] ?? '113';
                                $codeOWM = convertirCodeWeatherApiVersOpenWeatherMap($codeApi);
                                $heureIconUrl = "https://openweathermap.org/img/wn/{$codeOWM}@2x.png";
                                ?>
                                <div class="case-heure">
                                    <div class="carte-heure">
                                        <p class="fw-bold  "><?= date('H\h', strtotime($heure['time'])) ?></p>
                                        <img src="<?= htmlspecialchars($heureIconUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Icône météo" class="icone-meteo" />
                                        <p class="mb-0"><?= htmlspecialchars($heure['temp_c'], ENT_QUOTES, 'UTF-8') ?>°C</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button onclick="scrollRight()" class="btn-scroll">➡️</button>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-danger text-center mt-4">❌ Ville introuvable.</p>
    <?php endif; ?>
    
    <div class="text-center mt-4">
        <a href="previsions.php?ville=<?= urlencode($ville) ?>" class="btn btn-outline-primary btn-lg" style="background-color: white;">
        <?= $t['see_forecast'] ?>
        </a>
    </div>
    
    <div id="cookie-banner" class="cookie-modal-backdrop">
        <div class="cookie-modal-box">
            <h4> <?= $t['cookie_title'] ?>🍪</h4>
            <p><?= $t['cookie_text'] ?></p>
            <div class="cookie-actions">
                <button id="accept-cookies" class="btn btn-success"><?= $t['accept'] ?></button>
                <button id="refuse-cookies" class="btn btn-secondary"><?= $t['refuse'] ?></button>
            </div>
        </div>
    </div>

    <?php
    // Préparation des données pour le graphique
    $labels = [];
    $tempData = [];
    $humidData = [];
    $pressureData = [];
    $precipData = [];

    if (isset($heureAujourdHui)) {
        foreach ($heureAujourdHui as $heure) {
            $labels[] = date('H:i', strtotime($heure['time']));
            $tempData[] = $heure['temp_c'];
            $humidData[] = $heure['humidity'];
            $pressureData[] = $heure['pressure_mb'];
            $precipData[] = $heure['precip_mm'] ?? 0;
        }
    }
    ?>

    <section class="my-4">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                    <h2 class="h5 mb-0">Stats</h2>
                    <div class="btn-group btn-group-sm metric-switcher">
                        <button class="btn btn-metric active" data-metric="temp">🌡 <?= $t['temp'] ?></button>
                        <button class="btn btn-metric" data-metric="humidity">💧 <?= $t['humidity'] ?></button>
                        <button class="btn btn-metric" data-metric="pressure">🧭 Pression</button>
                        <button class="btn btn-metric" data-metric="precip">🌧 Pluie</button>
                    </div>
                </div>
                
                <div class="chart-container" style="height: 400px; width: 100%;">
                    <canvas id="weatherMasterChart"></canvas>
                </div>
                
                <div class="px-4 pb-3 text-end">
                    <small class="text-muted update-info">
                        <i class="bi bi-clock-history"></i> Dernière mise à jour: <?= date('H:i') ?>
                    </small>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("bouton-theme");
    if (!btn) {
        console.error(" Le bouton #bouton-theme n'existe pas !");
    } else {
        console.log(" Bouton thème détecté");
    }
});
</script>


<script>
//<![CDATA[
    window.chartHasData = <?= !empty($labels) ? 'true' : 'false' ?>;
    window.chartLabels = <?= json_encode($labels) ?>;
    window.chartTemp = <?= json_encode($tempData) ?>;
    window.chartHumid = <?= json_encode($humidData) ?>;
    window.chartPressure = <?= json_encode($pressureData) ?>;
    window.chartPrecip = <?= json_encode($precipData) ?>;
//]]>
</script>

<!-- Librairies -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>

<!-- Scripts personnalisés -->
<script src="js/theme-switcher.js"></script>
<script src="js/index.js"></script>
<script>
//<![CDATA[
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('ville') && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                fetch("includes/save_position.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ lat: lat, lon: lon })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.ville) {
                        window.location.href = "index.php?ville=" + encodeURIComponent(data.ville);
                    }
                });
            },
            function(err) {
                console.warn("Géolocalisation refusée", err);
            }
        );
    }
});
//]]>
</script>



<?php include 'includes/footer.php'; ?>
