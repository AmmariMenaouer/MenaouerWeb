<?php
session_start();
include_once 'includes/lang.php';

require_once 'functions_meteo_Api/config.php';
require_once 'functions_meteo_Api/utils.php';
require_once 'functions_meteo_Api/api.php';
require_once 'functions_meteo_Api/statistiques.php';
require_once 'functions_meteo_Api/enregistrementhistorique.php';
require_once 'functions_meteo_Api/enregistrement.php'; 

$ville = isset($_GET['ville']) ? htmlspecialchars($_GET['ville']) : 'Paris';
$coord = obtenirCoordonnees($ville);

// Fonction utilisée pour les icônes
function convertirCodeVersOWM($code) {
    $map = [
        113 => "01d", 116 => "02d", 119 => "03d", 122 => "04d", 143 => "50d",
        176 => "09d", 200 => "11d", 263 => "09d", 266 => "10d", 293 => "10d",
        296 => "10d", 299 => "09d", 302 => "10d", 308 => "10d", 320 => "13d",
        329 => "13d", 350 => "13d", 389 => "11d", 392 => "11d", 395 => "13d"
    ];
    return $map[$code] ?? "03d";
}

// Fonction de traduction de date
function traduireJourFrancais($date) {
    $jours = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
    $mois = ['Jan'=>'janv.','Feb'=>'févr.','Mar'=>'mars','Apr'=>'avr.','May'=>'mai','Jun'=>'juin','Jul'=>'juil.','Aug'=>'août','Sep'=>'sept.','Oct'=>'oct.','Nov'=>'nov.','Dec'=>'déc.'];
    $timestamp = strtotime($date);
    return ($jours[date('l', $timestamp)] ?? date('l', $timestamp)) . " " . date('d', $timestamp) . " " . ($mois[date('M', $timestamp)] ?? date('M', $timestamp)) . " " . date('Y', $timestamp);
}

// 🔁 Enregistrement historique utilisateur
if ($coord) {
    $resultat = obtenirMeteoActuelle($coord['lat'], $coord['lon']);
    $source = $resultat['source'];
    $data = $resultat['data'];

    if ($data) {
        if ($source === 'openweathermap') {
            enregistrerHistorique(
                $ville,
                $coord['lat'],
                $coord['lon'],
                $data['weather'][0]['description'] ?? '',
                $data['main']['temp'] ?? '',
                $data['main']['humidity'] ?? '',
                $data['main']['pressure'] ?? ''
            );
        } elseif ($source === 'weatherapi') {
            enregistrerHistorique(
                $ville,
                $coord['lat'],
                $coord['lon'],
                $data['current']['condition']['text'] ?? '',
                $data['current']['temp_c'] ?? '',
                $data['current']['humidity'] ?? '',
                $data['current']['pressure_mb'] ?? ''
            );
        }
    }
}
?>

<?php
require_once 'includes/head.inc.php';
include 'includes/header.inc.php';
?>

<main class="container py-5">
    <h1 class="text-center mb-4" style="color:white"><?= $t['forecast_7d'] ?></h1>

    <!-- Formulaire de recherche -->
    <form method="get" action="previsions.php" class="row justify-content-center mb-5">
        <div class="col-md-6">
            <input type="text" name="ville" class="form-control text-center" placeholder="Entrez une ville" value="<?= htmlspecialchars($ville); ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Voir</button>
        </div>
    </form>

    <?php if ($coord): ?>
        <?php
        $lat = $coord['lat'];
        $lon = $coord['lon'];
        $previsions = obtenirPrevisions7Jours($lat, $lon);
        $jours = $previsions['forecast']['forecastday'] ?? [];

        // 🔁 Enregistrement dans le CSV global
        enregistrerRechercheCSV($ville, $lat, $lon, $previsions);
        ?>

        <?php if ($jours): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($jours as $jour): ?>
                    <?php
                    $date = traduireJourFrancais($jour['date']);
                    $condition = $jour['day']['condition']['text'];
                    $code = $jour['day']['condition']['code'];
                    $iconeOWM = "https://openweathermap.org/img/wn/" . convertirCodeVersOWM($code) . "@2x.png";
                    $heuresJson = htmlspecialchars(json_encode($jour['hour']), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col">
                        <div class="card h-100 shadow">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $date ?></h5>
                                <img src="<?= $iconeOWM ?>" alt="<?= $condition ?>" width="60">
                                <p class="fw-bold mt-2"><?= ucfirst($condition) ?></p>
                                <p>🌡 <?= $jour['day']['mintemp_c'] ?>°C - <?= $jour['day']['maxtemp_c'] ?>°C</p>
                                <p>💧 <?= $jour['day']['avghumidity'] ?>% | 💨 <?= $jour['day']['maxwind_kph'] ?> km/h | 🌧 <?= $jour['day']['daily_chance_of_rain'] ?>%</p>
                                <button class="btn btn-outline-primary btn-heures mt-2" data-heures='<?= $heuresJson ?>'>Voir par heure</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- MODALE -->
            <div class="modal fade" id="modalHeures" tabindex="-1" aria-labelledby="modalHeuresLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalHeuresLabel">📆 Prévisions heure par heure</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body position-relative">
                            <button class="bouton-scroll" id="btn-gauche">⬅️</button>
                            <button class="bouton-scroll" id="btn-droite">➡️</button>
                            <div id="contenuHeures-wrapper">
                                <div id="contenuHeures"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-danger text-center">❌ Impossible de charger les prévisions météo.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-danger text-center">❌ Ville introuvable.</p>
    <?php endif; ?>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-outline-secondary">⬅️ Retour à l'accueil</a>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function convertirCodeVersOWM(code) {
    const map = {
        113: "01d", 116: "02d", 119: "03d", 122: "04d", 143: "50d",
        176: "09d", 200: "11d", 263: "09d", 266: "10d", 293: "10d",
        296: "10d", 299: "09d", 302: "10d", 308: "10d", 320: "13d",
        329: "13d", 350: "13d", 389: "11d", 392: "11d", 395: "13d"
    };
    return map[code] || "03d";
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalHeures'));
    const wrapper = document.getElementById('contenuHeures-wrapper');
    document.querySelectorAll('.btn-heures').forEach(btn => {
        btn.addEventListener('click', () => {
            const heures = JSON.parse(btn.dataset.heures);
            const conteneur = document.getElementById('contenuHeures');
            conteneur.innerHTML = '';
            heures.forEach(h => {
                const heureStr = new Date(h.time).getHours().toString().padStart(2, '0') + 'h';
                const icon = convertirCodeVersOWM(h.condition.code);
                const html = `
                    <div>
                        <p><strong>${heureStr}</strong></p>
                        <img src="https://openweathermap.org/img/wn/${icon}@2x.png" alt="${h.condition.text}" width="60">
                        <p>${h.condition.text}</p>
                        <p>🌡 ${h.temp_c}°C</p>
                        <p>💧 ${h.humidity}%</p>
                        <p>💨 ${h.wind_kph} km/h</p>
                        <p>🌧 ${h.chance_of_rain ?? 0}%</p>
                    </div>
                `;
                conteneur.insertAdjacentHTML('beforeend', html);
            });
            modal.show();
        });
    });

    document.getElementById('btn-gauche').addEventListener('click', () => {
        wrapper.scrollBy({ left: -300, behavior: 'smooth' });
    });

    document.getElementById('btn-droite').addEventListener('click', () => {
        wrapper.scrollBy({ left: 300, behavior: 'smooth' });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
