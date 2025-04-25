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
        file_put_contents(__DIR__ . '/data/debug_index.txt', "[AVERTISSEMENT] 📉 Prévisions manquantes pour $nom, enregistrement partiel forcé\n", FILE_APPEND);
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




<main class="container  ">
    <h1 class="text-center  " style="color: white;">Carte Interactive</h1>
    <p class="text-center  " style="color: white;">
        Séléctionnez un département sur la carte ci-dessous.
    </p>
    
    
    

    <!-- CARTE INTERACTIVE -->
    <section class="mb-5 text-center">
        <div class="weather-card d-inline-block border rounded shadow p-3 bg-white">
            <img src="images/carte_france.png" alt="Carte de France" usemap="#cartefrancepng" style="max-width: 100%; height: auto;" />
            <map name="cartefrancepng">
                <area shape="poly" alt="Hauts de France" title="" coords="516,22,479,26,459,53,460,95,454,114,467,162,469,204,564,220,580,236,591,190,615,180,631,128,612,108" href="region.php?nom=hdf" target="_self" />
                <area shape="poly" alt="Normandie" title="Normandie" coords="227,155,302,193,360,190,433,124,455,138,462,200,412,254,392,304,347,276,247,277,241,273" href="region.php?nom=norm" target="_self" />
                <area shape="poly" alt="Ile De France" title="Ile De France" coords="540,318,478,293,456,209,494,211,563,225,582,248,580,285,550,289,550,289" href="region.php?nom=idf" target="_self" />
                <area shape="poly" alt="Grand Est" title="Grand Est" coords="642,137,664,119,717,167,792,183,808,203,903,220,860,376,828,340,751,328,727,362,692,356,692,341,665,313,644,327,644,327,615,326,579,284,591,238,606,208" href="region.php?nom=ge" target="_self" />
                <area shape="poly" alt="Bretagne" title="Bretagne" coords="37,255,124,236,224,267,224,267,224,267,251,294,267,278,261,324,215,348,178,378,123,361,53,325,28,307,57,286" href="region.php?nom=bret" target="_self" />
                <area shape="poly" alt="Pays de la Loire" title="Pays de la Loire" coords="288,505,275,335,265,274,330,285,396,312,384,356,349,368,333,416,270,432,215,485,187,437,172,387,271,315" href="region.php?nom=pdll" target="_self" />
                <area shape="poly" alt="Centre Val de Loire" title="Centre Val de Loire" coords="511,493,421,490,389,434,342,415,367,373,412,357,405,295,414,258,456,253,478,301,553,334,531,369,557,454,505,480" href="region.php?nom=cvdl" target="_self" />
                <area shape="poly" alt="Bourgogne" title="Bourgogne" coords="621,525,692,474,765,505,834,367,752,322,731,365,689,355,657,319,539,362,571,290,551,293,585,290,548,371,565,454,610,459,635,497,631,499" href="region.php?nom=bfc" target="_self" />
                <area shape="poly" alt="Bordeaux" title="Bordeaux" coords="307,874,206,809,261,507,299,505,287,429,331,421,415,495,498,503,513,596,489,639,439,638,395,718,311,755,323,824,302,862" href="region.php?nom=na" target="_self" />
                <area shape="poly" alt="Occitanie" title="Occitanie" coords="565,912,325,870,331,762,451,647,513,692,589,670,680,735,655,797,569,825,554,856" href="region.php?nom=occ" target="_self" />
                <area shape="poly" alt="Auvergne" title="Auvergne" coords="754,726,647,711,578,653,496,669,539,459,614,473,614,523,688,533,696,481,827,521,853,605,795,629,740,677,731,706" href="region.php?nom=ara" target="_self" />
                <area shape="poly" alt="Provences Cote d'Azue" title="Provences Cote d'Azue" coords="690,730,664,797,820,836,909,734,849,708,823,628,743,685,757,731" href="region.php?nom=paca" target="_self" />
                <area shape="poly" alt="Corse" title="Corse" coords="964,783,886,830,924,929,987,931,971,823" href="region.php?nom=corse" target="_self" />
            </map>
        </div>
    </section>

    

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
</main>
<?php include 'includes/footer.php'; ?>
