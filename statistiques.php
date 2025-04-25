<?php
session_start();
include_once 'includes/lang.php';
include 'includes/head.inc.php';

include_once 'includes/header.inc.php';

$csvFilePath = 'data/recherches.csv';
$villes = [];
$departements = [];
$regions = [];

if (file_exists($csvFilePath) && is_readable($csvFilePath)) {
    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        if ($headers !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $entry = array_combine($headers, $row);
                    $ville = $entry['Ville recherchée'] ?? '';
                    $dep = $entry['Département'] ?? '';
                    $region = $entry['Région'] ?? '';

                    if ($ville !== '') $villes[$ville] = ($villes[$ville] ?? 0) + 1;
                    if ($dep !== '') $departements[$dep] = ($departements[$dep] ?? 0) + 1;
                    if ($region !== '') $regions[$region] = ($regions[$region] ?? 0) + 1;
                }
            }
        }
        fclose($handle);
    }
}

arsort($villes);
arsort($departements);
arsort($regions);
$topVilles = array_slice($villes, 0, 10, true);
$topDeps = array_slice($departements, 0, 10, true);
$topRegions = array_slice($regions, 0, 10, true);
?>
<main class="container  ">
    <h1 class="text-center  " style="color:white; margin-top: 30px; margin-bottom:30px"><?= $t['statistics'] ?></h1>

    <!-- Graphique Villes -->
    <section class=" md-4 bg-white p-4 rounded shadow">
        <h2 class="h5 text-center  ">Villes les plus recherchées</h2>
        <canvas id="chartVilles" role="img" aria-label="Graphique des villes les plus recherchées"></canvas>
    </section>

    <!-- Graphique Départements -->
    <section class="  bg-white p-4 rounded shadow">
        <h2 class="h5 text-center  ">Départements les plus recherchés</h2>
        <canvas id="chartDepartements" role="img" aria-label="Graphique des départements les plus recherchés"></canvas>
    </section>

    <!-- Graphique Régions -->
    <section class="  bg-white p-4 rounded shadow">
        <h2 class="h5 text-center  ">Régions les plus recherchées</h2>
        <canvas id="chartRegions" role="img" aria-label="Graphique des régions les plus recherchées"></canvas>
    </section>

    


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const villesLabels = <?= json_encode(array_keys($topVilles)) ?>;
    const villesData = <?= json_encode(array_values($topVilles)) ?>;
    const depLabels = <?= json_encode(array_keys($topDeps)) ?>;
    const depData = <?= json_encode(array_values($topDeps)) ?>;
    const regLabels = <?= json_encode(array_keys($topRegions)) ?>;
    const regData = <?= json_encode(array_values($topRegions)) ?>;

    function drawChart(id, labels, data, labelText) {
        new Chart(document.getElementById(id), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: labelText,
                    data: data,
                    backgroundColor: 'rgba(33, 150, 243, 0.7)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre de recherches'
                        }
                    }
                }
            }
        });
    }

    drawChart('chartVilles', villesLabels, villesData, 'Recherches par ville');
    drawChart('chartDepartements', depLabels, depData, 'Recherches par département');
    drawChart('chartRegions', regLabels, regData, 'Recherches par région');
</script>
</main>

<?php include_once 'includes/footer.php'; ?>
