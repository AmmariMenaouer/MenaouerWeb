/**
 * @fileoverview Ce script initialise un graphique en barres affichant le nombre de recherches
 * effectuées pour différentes villes. Il utilise la bibliothèque Chart.js et attend que
 * le DOM soit complètement chargé pour démarrer.
 *
 * Les données sont fournies via deux variables globales :
 * - `window.chartVillesLabels` : un tableau contenant les noms des villes.
 * - `window.chartVillesData` : un tableau contenant le nombre de recherches correspondantes.
 *
 * Le graphique est affiché dans un élément <canvas> ayant l'ID `villeChart`.
 *
 * @requires Chart.js
 */

/**
 * Initialise un graphique en barres avec les données des villes et leurs recherches.
 *
 * @event DOMContentLoaded
 * @param {Event} event - L'événement déclenché lorsque le DOM est complètement chargé.
 * 
 * @global {string[]} window.chartVillesLabels - Les noms des villes utilisées comme étiquettes du graphique.
 * @global {number[]} window.chartVillesData - Le nombre de recherches par ville, utilisé pour la hauteur des barres.
 *
 * @returns {void} Ne retourne rien.
 * @author groupe 7
 */
document.addEventListener('DOMContentLoaded', function initVilleChart(event) {
    const ctx = document.getElementById('villeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.chartVillesLabels,
            datasets: [{
                label: 'Nombre de recherches',
                data: window.chartVillesData,
                backgroundColor: 'rgba(13, 110, 253, 0.7)', // bleu transparent
                borderColor: 'rgba(13, 110, 253, 1)',      // bleu opaque
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
