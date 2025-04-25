

<?php
require_once __DIR__ . '/includes/head.inc.php';
include 'includes/header.inc.php';
include_once 'includes/lang.php';

$regions = [
    'idf' => 'Île-de-France',
    'na' => 'Nouvelle-Aquitaine',
    'paca' => 'Provence-Alpes-Côte d’Azur',
    'occ' => 'Occitanie',
    'ara' => 'Auvergne-Rhône-Alpes',
    'ge' => 'Grand Est',
    'bfc' => 'Bourgogne-Franche-Comté',
    'cvdl' => 'Centre-Val de Loire',
    'pdll' => 'Pays de la Loire',
    'bret' => 'Bretagne',
    'norm' => 'Normandie',
    'hdf' => 'Hauts-de-France',
    'corse' => 'Corse',
];

$ids = [
    'idf' => '11', 'na' => '75', 'paca' => '93', 'occ' => '76',
    'ara' => '84', 'ge' => '44', 'bfc' => '27', 'cvdl' => '24',
    'pdll' => '52', 'bret' => '53', 'norm' => '28', 'hdf' => '32', 'corse' => '94',
];

$code_region = $_GET['nom'] ?? '';
$region_nom = $regions[$code_region] ?? 'Région inconnue';
$region_id = $ids[$code_region] ?? null;

$departments = [];
$departmentsPath = __DIR__ . '/csv/departments.csv';
if ($region_id && file_exists($departmentsPath) && ($handle = fopen($departmentsPath, 'r')) !== false) {
    fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== false) {
        if ($data[1] === $region_id) {
            $departments[$data[2]] = $data[3];
        }
    }
    fclose($handle);
}

$villes = [];
$selected_dep = $_GET['dep'] ?? '';
$citiesPath = __DIR__ . '/csv/cities.csv';
if (!empty($selected_dep) && file_exists($citiesPath) && ($handle = fopen($citiesPath, 'r')) !== false) {
    fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== false) {
        if ($data[1] === $selected_dep) {
            $villes[] = $data[4] . ' (' . $data[3] . ')';
        }
    }
    fclose($handle);
}
?>



<h2 class="text-center mt-4">Vous avez sélectionné : <?= htmlspecialchars($region_nom) ?></h2>

<section class="selection-region">
    <h2>Choisissez votre département puis votre ville</h2>

    <form method="get" class="selection-departement">
        <input type="hidden" name="nom" value="<?= htmlspecialchars($code_region) ?>" />
        <label>Département :</label>
        <select name="dep" class="select-input" onchange="this.form.submit()">
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($departments as $code => $depName): ?>
                <option value="<?= htmlspecialchars($code) ?>"<?= $code === $selected_dep ? ' selected' : '' ?>>
                    <?= htmlspecialchars($depName) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($villes)): ?>
        <form method="get" action="previsions.php" class="selection-ville">
            <input type="hidden" name="nom" value="<?= htmlspecialchars($code_region) ?>" />
            <input type="hidden" name="dep" value="<?= htmlspecialchars($selected_dep) ?>" />
            <label>Ville :</label>
            <select name="ville" class="select-input" required>
                <?php foreach ($villes as $ville): ?>
                    <?php preg_match('/^(.*) \((\d+)\)$/', $ville, $match); $villeNom = $match[1] ?? $ville; ?>
                    <option value="<?= htmlspecialchars($villeNom) ?>"><?= htmlspecialchars($ville) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-valider">Valider</button>
        </form>
    <?php endif; ?>

    <a href="index.php" class="retour-lien">← Retour à la carte</a>
</section>

<a href="index.php" class="btn-retour" title="Retour à l'accueil">🏠</a>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

