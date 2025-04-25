/**
 * @fileoverview Affiche les prévisions heure par heure dans une modale Bootstrap
 * lorsqu'un bouton `.btn-heures` est cliqué. Utilise les données météo associées à chaque bouton.
 *
 * @requires Bootstrap 5
 */

/**
 * Lors du chargement du DOM, attache des écouteurs à chaque bouton `.btn-heures`.
 * Affiche dans une modale les prévisions heure par heure avec icône, température et heure.
 *
 * @event DOMContentLoaded
 * @returns {void}
 * @author groupe 7
 */
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.btn-heures').forEach(btn => {
        btn.addEventListener('click', () => {
            /**
             * @type {Array<Object>} heures - Tableau des données météo heure par heure.
             */
            const heures = JSON.parse(btn.getAttribute('data-heures'));

            const container = document.getElementById('contenuHeures');
            container.innerHTML = '';

            heures.forEach(h => {
                const heure = new Date(h.time).getHours() + "h";
                const temp = h.temp_c + "°C";

                // Extraction du code d’icône depuis l’URL WeatherAPI
                const iconCode = h.condition.icon.match(/(\d+)\.png$/)?.[1] || '113';

                // Conversion vers le code d’icône OpenWeatherMap
                const codeOWM = convertirCodeVersOWM(iconCode);
                const iconUrl = `https://openweathermap.org/img/wn/${codeOWM}@2x.png`;

                container.innerHTML += `
                    <div class="col">
                        <div class="border rounded p-2 h-100">
                            <p class="fw-bold  ">${heure}</p>
                            <img src="${iconUrl}" style="width: 50px;">
                            <p class="mb-0">${temp}</p>
                        </div>
                    </div>`;
            });

            // Affiche la modale avec les heures
            new bootstrap.Modal(document.getElementById('modalHeures')).show();
        });
    });
});

/**
 * Convertit un code d'icône WeatherAPI vers un code d'icône OpenWeatherMap.
 *
 * @function
 * @param {string} code - Code d’icône fourni par WeatherAPI (ex. : "113").
 * @returns {string} Code d’icône compatible OpenWeatherMap (ex. : "01d").
 */
function convertirCodeVersOWM(code) {
    const map = {
        '113': '01d',
        '116': '02d',
        '119': '03d',
        '122': '04d',
        '143': '50d',
        '176': '09d',
        '200': '11d',
        '263': '09d',
        '266': '10d',
        '293': '10d',
        '296': '10d',
        '299': '09d',
        '302': '10d',
        '308': '10d',
        '320': '13d',
        '329': '13d',
        '350': '13d',
        '389': '11d',
        '392': '11d',
        '395': '13d'
    };
    return map[code] || '01d';
}


