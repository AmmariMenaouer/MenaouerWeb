<?php
require_once 'enregistrementhistorique.php';

$fichier = getHistoriquePath();
$nomFichierTelechargement = 'historique_meteo_utilisateur.csv';

if (!file_exists($fichier)) {
    header('HTTP/1.0 404 Not Found');
    echo "Aucun historique disponible à télécharger.";
    exit;
}

header('Content-Type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=\"$nomFichierTelechargement\"");
header('Pragma: no-cache');
header('Expires: 0');

readfile($fichier);
exit;
