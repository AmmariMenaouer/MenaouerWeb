<?php
/**
 * sit web NASA API Viewer avec géolocalisation
 *
 * Ce script récupère l'image astronomique du jour (APOD) de la NASA
 * et permet à l'utilisateur de partager sa position GPS.
 * Les données peuvent être affichées en HTML, JSON ou XML.
 */

// 1. CONFIGURATION DES API
// Clé API pour accéder aux services NASA
$nasa_api_key = "0kh1h3YwfWjuZrfQzBkFAMXK5sOmZrtd5TK5NfBv";

// 2. DÉTERMINATION DU FORMAT DE SORTIE
// Par défaut en HTML, mais peut être JSON ou XML via le paramètre GET 'format'
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'html';

// 3. RÉCUPÉRATION DE L'IMAGE ASTRONOMIQUE DU JOUR (APOD)
$apod_data = [];       // Stocke les données APOD
$apod_error = null;    // Stocke les erreurs éventuelles
$apod_raw = null;      // Stocke la réponse brute de l'API

try {
    // Construction de l'URL pour l'API APOD de la NASA
    $apod_url = "https://api.nasa.gov/planetary/apod?api_key=$nasa_api_key";

    // Récupération des données
    $apod_raw = file_get_contents($apod_url);

    // Décodage de la réponse JSON
    $apod_data = json_decode($apod_raw, true);

    // Vérification que les données ont bien été décodées
    if (!$apod_data) {
        throw new Exception("Erreur de décodage des données APOD");
    }
} catch (Exception $e) {
    // Gestion des erreurs
    $apod_error = "Erreur lors de la récupération de l'APOD: " . $e->getMessage();
}

// 4. GESTION DES FORMATS DE SORTIE BRUTS (JSON/XML)
if ($format === 'json' || $format === 'xml') {
    // En-tête HTTP approprié selon le format
    header('Content-Type: application/' . $format);

    // Préparation des données de sortie
    $output = [
        'apod' => $apod_error ? ['error' => $apod_error] : $apod_data,
        'timestamp' => date('c') // Date au format ISO 8601
    ];

    // Conversion en XML si demandé
    if ($format === 'xml') {
        /**
         * Convertit un tableau en structure XML
         * @param array $data Données à convertir
         * @param SimpleXMLElement $xml_data Élément XML parent
         */
        function array_to_xml($data, &$xml_data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    if (is_numeric($key)) {
                        $key = 'item'.$key;
                    }
                    $subnode = $xml_data->addChild($key);
                    array_to_xml($value, $subnode);
                } else {
                    $xml_data->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }

        // Création de la structure XML de base
        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml($output, $xml_data);
        echo $xml_data->asXML();
    }
    // Conversion en JSON si demandé
    else {
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit; // Arrêt du script après avoir envoyé les données brutes
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NASA APOD & Géolocalisation</title>
    <!-- Intégration de Leaflet pour les cartes -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Reset CSS de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Style du corps de la page */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0a0e23;
            background-image: radial-gradient(circle at 50% 50%, #1a2a6c, #0a0e23);
            color: #ffffff;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Conteneur principal */
        .container {
            background-color: rgba(16, 24, 64, 0.8);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.8s ease-out;
            margin-bottom: 30px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Titre principal */
        h1 {
            font-family: 'Space Mono', monospace;
            font-size: 2.8em;
            margin-bottom: 25px;
            color: #4fc3f7;
            text-shadow: 0 0 10px rgba(79, 195, 247, 0.5);
            letter-spacing: 1px;
            text-align: center;
        }

        /* Sections */
        .section {
            background-color: rgba(16, 24, 64, 0.6);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            width: 100%;
            border-left: 4px solid #4fc3f7;
        }

        .section h2 {
            color: #64ffda;
            font-family: 'Space Mono', monospace;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        /* Media container */
        .media-container {
            margin: 20px 0;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            background-color: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-container img,
        .media-container iframe {
            width: 100%;
            max-width: 900px;
            max-height: 600px;
            object-fit: contain;
            border-radius: 12px;
            display: block;
        }

        /* Description */
        .description {
            margin-top: 20px;
            font-size: 1.1em;
            line-height: 1.7;
            color: #e0f7fa;
        }

        /* Carte */
        #map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Boutons */
        .btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #4fc3f7, #2196f3);
            border: none;
            border-radius: 8px;
            color: #0a0e23;
            font-size: 1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background: linear-gradient(135deg, #64ffda, #00bcd4);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Info de localisation */
        .location-info {
            background-color: rgba(16, 24, 64, 0.6);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #64ffda;
        }

        .location-info h3 {
            color: #4fc3f7;
            margin-bottom: 10px;
        }

        .location-info p {
            margin-bottom: 10px;
            color: #e0f7fa;
        }

        /* Liens bruts */
        .raw-links {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .raw-links a {
            margin-right: 15px;
            color: #4fc3f7;
            text-decoration: none;
            transition: color 0.3s;
        }

        .raw-links a:hover {
            color: #64ffda;
            text-decoration: underline;
        }

        /* Message d'erreur */
        .error {
            background-color: rgba(239, 83, 80, 0.2);
            border: 1px solid #ef5350;
            border-radius: 8px;
            padding: 20px;
            color: #ffcdd2;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Pied de page */
        footer {
            margin-top: 50px;
            font-size: 0.9em;
            color: #b3e5fc;
            text-align: center;
            opacity: 0.8;
            width: 100%;
        }

        footer p {
            margin: 5px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 2em;
            }

            .section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Section APOD (Astronomy Picture of the Day) -->
    <div class="section">
        <h2>Image Astronomique du Jour (NASA APOD)</h2>
        <?php if ($apod_error): ?>
            <div class="error"><?= htmlspecialchars($apod_error) ?></div>
        <?php else: ?>
            <div class="media-container">
                <?php if ($apod_data['media_type'] === 'image'): ?>
                    <img src="<?= htmlspecialchars($apod_data['url'] ?? '') ?>" alt="Image astronomique du jour">
                <?php else: ?>
                    <iframe src="<?= htmlspecialchars($apod_data['url'] ?? '') ?>" width="100%" height="500" title="Média astronomique du jour" allowfullscreen></iframe>
                <?php endif; ?>
            </div>

            <div class="description">
                <h3><?= htmlspecialchars($apod_data['title'] ?? '') ?></h3>
                <p><?= htmlspecialchars($apod_data['explanation'] ?? '') ?></p>
                <?php if (!empty($apod_data['copyright'])): ?>
                    <p class="copyright">© <?= htmlspecialchars($apod_data['copyright']) ?></p>
                <?php endif; ?>
            </div>

            <div class="raw-links">
                <strong>Voir les données brutes :</strong>
                <a href="?format=json" target="_blank">JSON</a>
                <a href="?format=xml" target="_blank">XML</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section de géolocalisation -->
    <div class="section">
        <h2>Votre Position Exacte</h2>
        <p>Cliquez pour partager votre position GPS :</p>
        <button id="getLocation" class="btn">Obtenir ma position</button>

        <div id="map"></div>

        <div id="locationInfo" class="location-info" style="display:none">
            <h3>Détails de position :</h3>
            <p id="positionDetails"></p>
            <p id="addressDetails"></p>
        </div>

        <div class="raw-links">
            <strong>Voir les données brutes :</strong>
            <a href="?format=json" target="_blank">JSON</a>
            <a href="?format=xml" target="_blank">XML</a>
        </div>
    </div>
</div>

<footer>
    <p>Ce site utilise l'API APOD de la NASA</p>
    <p>Développé avec Leaflet pour la géolocalisation</p>
</footer>

<!-- Scripts JavaScript -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialisation de la carte Leaflet
    const map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Gestionnaire d'événement pour le bouton de géolocalisation
    document.getElementById('getLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.textContent = "Localisation en cours...";

            // Demande de la position actuelle
            navigator.geolocation.getCurrentPosition(
                // Callback de succès
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    // Centrer la carte sur la position
                    map.setView([lat, lon], 15);

                    // Ajouter un marqueur à la position
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup("Vous êtes ici")
                        .openPopup();

                    // Ajouter un cercle représentant la précision
                    L.circle([lat, lon], {
                        radius: accuracy,
                        color: '#3388ff',
                        fillOpacity: 0.2
                    }).addTo(map);

                    // Afficher les détails de position
                    document.getElementById('locationInfo').style.display = 'block';
                    document.getElementById('positionDetails').innerHTML = `
                            <strong>Coordonnées :</strong> ${lat.toFixed(6)}, ${lon.toFixed(6)}<br>
                            <strong>Précision :</strong> ±${Math.round(accuracy)} mètres
                        `;

                    // Récupération de l'adresse via l'API Nominatim
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            const address = data.address || {};
                            let addressStr = '';
                            if (address.road) addressStr += `${address.road}, `;
                            if (address.postcode) addressStr += `${address.postcode} `;
                            if (address.city || address.town) addressStr += `${address.city || address.town}, `;
                            if (address.country) addressStr += address.country;

                            document.getElementById('addressDetails').innerHTML =
                                `<strong>Adresse approximative :</strong> ${addressStr}`;
                        });
                },
                // Callback d'erreur
                function(error) {
                    alert("Erreur : " + (
                        error.code === error.PERMISSION_DENIED ?
                            "Vous avez refusé la localisation" :
                            "Impossible d'obtenir votre position"
                    ));
                    document.getElementById('getLocation').textContent = "Réessayer";
                },
                // Options de géolocalisation
                {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        } else {
            alert("Votre navigateur ne supporte pas la géolocalisation");
        }
    });
</script>
</body>
</html>