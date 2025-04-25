<?php

//  Fonction universelle pour appeler une API (JSON ou XML)
/**
 * Appelle une API via HTTP GET et retourne les données sous forme de tableau associatif,
 * qu'elles soient en JSON ou XML.
 *
 * @param string $url URL complète de l'API à interroger.
 * @return array|null Retourne un tableau associatif ou null en cas d'erreur.
 */
function appelApi($url) {
    // Créer un contexte HTTP avec méthode GET et un délai d'attente de 5 secondes
    $opts = ['http' => ['method' => 'GET', 'timeout' => 5]];
    $ctx = stream_context_create($opts);

    // Envoyer la requête avec gestion d'erreur silencieuse
    $res = @file_get_contents($url, false, $ctx);

    // Si aucune réponse reçue, retourner null
    if (!$res) return null;

    // Tenter de parser la réponse comme XML
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($res);
    if ($xml !== false) {
        // Convertir le XML en JSON puis en tableau associatif
        return json_decode(json_encode($xml), true);
    }

    // Si ce n'est pas du XML, tenter de parser en JSON
    return json_decode($res, true);
}

// ☁️ Convertir une description météo (texte) vers un code d’icône OpenWeatherMap
/**
 * Convertit une description météo textuelle en un code d’icône OpenWeatherMap.
 *
 * @param string $desc Description météo (en anglais ou français).
 * @return string Code d’icône OpenWeatherMap (ex. '01d' pour ciel clair).
 */
function convertirDescriptionVersCode($desc) {
    $desc = strtolower($desc); // Normaliser en minuscules

    // Analyse de mots-clés dans la description
    if (str_contains($desc, 'clear') || str_contains($desc, 'soleil')) return '01d';      // Clair
    if (str_contains($desc, 'partly') || str_contains($desc, 'quelques nuages')) return '02d'; // Partiellement nuageux
    if (str_contains($desc, 'cloud') || str_contains($desc, 'nuageux')) return '03d';      // Nuageux
    if (str_contains($desc, 'overcast')) return '04d';                                     // Couvert
    if (str_contains($desc, 'rain') || str_contains($desc, 'pluie')) return '10d';         // Pluie
    if (str_contains($desc, 'shower')) return '09d';                                       // Averses
    if (str_contains($desc, 'thunder') || str_contains($desc, 'orage')) return '11d';      // Orage
    if (str_contains($desc, 'snow') || str_contains($desc, 'neige')) return '13d';         // Neige
    if (str_contains($desc, 'fog') || str_contains($desc, 'brouillard')) return '50d';     // Brouillard

    return '03d'; // Par défaut : nuageux
}

// 🔁 Convertir un code WeatherAPI vers un code d’icône OpenWeatherMap
/**
 * Convertit un code météo de WeatherAPI en un code d'icône OpenWeatherMap.
 *
 * @param string|int $codeWeatherApi Code météo (ex. '113', '122', etc.)
 * @return string Code d'icône OpenWeatherMap correspondant (ex. '01d').
 */
function convertirCodeWeatherApiVersOpenWeatherMap($codeWeatherApi) {
    $correspondance = [
        '113' => '01d', // Ensoleillé
        '116' => '02d', // Partiellement nuageux
        '119' => '03d', // Nuageux
        '122' => '04d', // Très nuageux
        '143' => '50d', // Brume
        '176' => '09d', // Pluie légère
        '200' => '11d', // Orage
        '263' => '09d', '266' => '10d', // Pluie légère à modérée
        '293' => '10d', '296' => '10d', // Pluie
        '299' => '09d', '302' => '10d', // Forte pluie
        '308' => '10d',                 // Pluie torrentielle
        '320' => '13d', '329' => '13d', '350' => '13d', // Neige
        '389' => '11d', '392' => '11d', // Orage avec pluie/neige
        '395' => '13d'  // Neige forte
    ];

    // Retourner le code OpenWeatherMap correspondant, ou '03d' par défaut
    return $correspondance[$codeWeatherApi] ?? '03d';



    
    // Autres fonctions déjà dans utils.php...
    

function getUserID() {
    if (!isset($_COOKIE['user_id'])) {
        $id = 'user_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . uniqid());
        setcookie('user_id', $id, time() + (365 * 24 * 60 * 60), "/");
        return $id;
    }

    return $_COOKIE['user_id'];
}





}
