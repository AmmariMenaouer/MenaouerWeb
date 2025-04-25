<?

include_once 'includes/lang.php';
require_once 'functions_meteo_Api/config.php';
require_once 'functions_meteo_Api/utils.php'; // Doit contenir getUserID()
require_once 'functions_meteo_Api/api.php';
require_once 'functions_meteo_Api/statistiques.php';
require_once 'functions_meteo_Api/enregistrement.php';
require_once 'functions_meteo_Api/enregistrementhistorique.php'; 
function enregistrerRechercheCSV($ville, $lat, $lon, $previsions7) {
    $log = __DIR__ . '/../logs/debug_csv.txt';
    $fichier = __DIR__ . '/../data/recherches.csv';

    if (!file_exists(dirname($fichier))) mkdir(dirname($fichier), 0777, true);
    if (!file_exists(dirname($log))) mkdir(dirname($log), 0777, true);

    file_put_contents($log, "📥 [$ville] Tentative d'enregistrement CSV...\n", FILE_APPEND);

    // Valeurs par défaut
    $date = date('Y-m-d');
    $condition = 'Inconnue';
    $tempMin = $tempMax = $tempMoy = $vent = $pluie = $humidite = $chancePluie = null;

    if ($previsions7 && isset($previsions7['forecast']['forecastday'][0])) {
        $jour = $previsions7['forecast']['forecastday'][0];
        $d = $jour['day'];
        $date = $jour['date'] ?? $date;
        $condition = $d['condition']['text'] ?? 'Inconnue';
        $tempMin = $d['mintemp_c'] ?? null;
        $tempMax = $d['maxtemp_c'] ?? null;
        $tempMoy = $d['avgtemp_c'] ?? null;
        $vent = $d['maxwind_kph'] ?? null;
        $pluie = $d['totalprecip_mm'] ?? null;
        $humidite = $d['avghumidity'] ?? null;
        $chancePluie = $d['daily_chance_of_rain'] ?? null;
    } else {
        file_put_contents($log, "⚠️ Données météo incomplètes pour $ville — enregistrement partiel\n", FILE_APPEND);
    }

    // Infos géographiques
    $departement = $region = 'Inconnu';
    $infos = @file_get_contents("https://geo.api.gouv.fr/communes?nom=" . urlencode($ville) . "&fields=departement,region&format=json");
    $infos = json_decode($infos, true);
    if (is_array($infos) && isset($infos[0])) {
        $departement = $infos[0]['departement']['nom'] ?? 'Inconnu';
        $region = $infos[0]['region']['nom'] ?? 'Inconnue';
    }

    // IP et géolocalisation
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $geo = @file_get_contents("http://ip-api.com/json/$ip");
    $geoData = json_decode($geo, true);
    $villeIP = $geoData['city'] ?? 'Inconnue';
    $paysIP = $geoData['country'] ?? 'Inconnu';

    // Ligne à enregistrer
    $ligne = [
        date('Y-m-d H:i:s'), $ville, $departement, $region,
        $lat, $lon, $date,
        $condition, $tempMin, $tempMax, $tempMoy, $vent,
        $pluie, $humidite, $chancePluie, $ip, $villeIP, $paysIP
    ];

    // En-têtes (écrits une seule fois)
    $entetes = [
        'Horodatage', 'Ville recherchée', 'Département', 'Région',
        'Latitude', 'Longitude', 'Date météo', 'Condition météo',
        'Température min (°C)', 'Température max (°C)', 'Température moyenne (°C)',
        'Vent max (km/h)', 'Précipitations (mm)', 'Humidité (%)',
        'Chance de pluie (%)', 'Adresse IP', 'Ville de l\'IP', 'Pays de l\'IP'
    ];

    $nouveau = !file_exists($fichier) || filesize($fichier) === 0;
    $f = @fopen($fichier, 'a');
    if (!$f) {
        file_put_contents($log, "❌ Impossible d'écrire dans $fichier\n", FILE_APPEND);
        return;
    }

    if ($nouveau) fputcsv($f, $entetes);
    fputcsv($f, $ligne);
    fclose($f);

    file_put_contents($log, "✅ Recherche enregistrée pour $ville dans $fichier\n", FILE_APPEND);
}
?>