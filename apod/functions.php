<?php
// Fonction pour appeler l'API
function appelApiMeteo($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FAILONERROR => true
    ]);
    $reponse = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Erreur API: " . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($reponse, true);
}

// Traitement des données météo
function getWeatherData($ville, $apiKey, $lang) {
    if (empty($ville)) {
        return ['error' => 'Veuillez entrer une ville'];
    }

    try {
        $urlApi = "https://api.openweathermap.org/data/2.5/weather?q=".urlencode($ville)."&appid=".$apiKey."&units=metric&lang=".$lang;
        $donneesBrutes = appelApiMeteo($urlApi);

        if (isset($donneesBrutes['cod']) && $donneesBrutes['cod'] === 200) {
            return [
                'ville' => $donneesBrutes['name'],
                'pays' => $donneesBrutes['sys']['country'],
                'temperature' => round($donneesBrutes['main']['temp']),
                'ressenti' => round($donneesBrutes['main']['feels_like']),
                'humidite' => $donneesBrutes['main']['humidity'],
                'vent' => round($donneesBrutes['wind']['speed'] * 3.6),
                'description' => ucfirst($donneesBrutes['weather'][0]['description']),
                'icone' => $donneesBrutes['weather'][0]['icon'],
                'lever_soleil' => date('H:i', $donneesBrutes['sys']['sunrise']),
                'coucher_soleil' => date('H:i', $donneesBrutes['sys']['sunset']),
                'pressure' => $donneesBrutes['main']['pressure']
            ];
        } else {
            return ['error' => $donneesBrutes['message'] ?? 'Ville non trouvée'];
        }
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}
?>