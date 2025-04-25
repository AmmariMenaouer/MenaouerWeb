/**
 * @fileoverview Gère l'affichage du bandeau de cookies, le scroll horizontal,
 * le graphique météo multi-métriques avec Chart.js, et les préférences de thème (mode sombre).
 * @requires Chart.js
 * @author Groupe 7
 */

document.addEventListener('DOMContentLoaded', () => {
    // 🍪 Bandeau de cookies
    const banner = document.getElementById('cookie-banner');
    if (!document.cookie.includes('consentement=oui') && !document.cookie.includes('consentement=non')) {
        banner.style.display = 'flex';
    }

    document.getElementById('accept-cookies')?.addEventListener('click', () => {
        document.cookie = "consentement=oui; path=/; max-age=" + (60 * 60 * 24 * 365);
        banner.style.display = 'none';
        location.reload();
    });

    document.getElementById('refuse-cookies')?.addEventListener('click', () => {
        document.cookie = "consentement=non; path=/; max-age=" + (60 * 60 * 24 * 365);
        banner.style.display = 'none';
    });

    // ➡️⬅️ Scroll horizontal
    const scroller = document.getElementById('scroller');
    if (scroller) {
        window.scrollLeft = () => scroller.scrollBy({ left: -200, behavior: 'smooth' });
        window.scrollRight = () => scroller.scrollBy({ left: 200, behavior: 'smooth' });
    }

    // 📈 Initialisation du graphique météo
    if (!window.chartHasData) {
        console.warn('Aucune donnée disponible pour le graphique');
        return;
    }

    const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

    const colors = {
        temp: isDarkMode ? 'rgba(255, 99, 132, 1)' : 'rgba(220, 53, 69, 1)',
        humidity: isDarkMode ? 'rgba(54, 162, 235, 1)' : 'rgba(13, 110, 253, 1)',
        pressure: isDarkMode ? 'rgba(255, 206, 86, 1)' : 'rgba(255, 193, 7, 1)',
        precip: isDarkMode ? 'rgba(75, 192, 192, 1)' : 'rgba(25, 135, 84, 1)',
        grid: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
        text: isDarkMode ? 'rgba(255, 255, 255, 0.9)' : 'rgba(0, 0, 0, 0.9)'
    };

    const ctx = document.getElementById('weatherMasterChart')?.getContext('2d');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.chartLabels,
            datasets: [
                {
                    label: 'Température (°C)',
                    data: window.chartTemp,
                    borderColor: colors.temp,
                    backgroundColor: hexToRgba(colors.temp, 0.15),
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y',
                    pointRadius: 0,
                    pointHoverRadius: 6
                },
                {
                    label: 'Humidité (%)',
                    data: window.chartHumid,
                    borderColor: colors.humidity,
                    backgroundColor: hexToRgba(colors.humidity, 0.15),
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y1',
                    hidden: true
                },
                {
                    label: 'Pression (hPa)',
                    data: window.chartPressure,
                    borderColor: colors.pressure,
                    backgroundColor: hexToRgba(colors.pressure, 0.15),
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y2',
                    hidden: true
                },
                {
                    label: 'Précipitations (mm)',
                    data: window.chartPrecip,
                    backgroundColor: hexToRgba(colors.precip, 0.5),
                    borderColor: colors.precip,
                    borderWidth: 1,
                    yAxisID: 'y3',
                    type: 'bar',
                    hidden: true,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDarkMode ? '#333' : '#fff',
                    titleColor: isDarkMode ? '#fff' : '#000',
                    bodyColor: isDarkMode ? '#eee' : '#111',
                    callbacks: {
                        label: ctx => {
                            const label = ctx.dataset.label || '';
                            return `${label}: ${ctx.parsed.y} ${getUnit(ctx.datasetIndex)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        parser: 'HH:mm',
                        unit: 'hour',
                        displayFormats: { hour: 'HH[h]' }
                    },
                    ticks: { color: colors.text },
                    grid: { color: colors.grid }
                },
                y: {
                    ticks: { color: colors.text },
                    grid: { color: colors.grid }
                },
                y1: { display: false },
                y2: { display: false },
                y3: { display: false }
            }
        }
    });

    // 🎚️ Sélection dynamique de métrique
    document.querySelectorAll('.btn-metric').forEach(btn => {
        btn.addEventListener('click', () => {
            const metric = btn.dataset.metric;
            document.querySelectorAll('.btn-metric').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const map = { temp: 0, humidity: 1, pressure: 2, precip: 3 };

            chart.data.datasets.forEach((ds, i) => {
                ds.hidden = i !== map[metric];
                chart.options.scales[`y${i ? i : ''}`].display = i === map[metric];
            });

            chart.update();
        });
    });

    // 🔄 Rechargement si le thème du système change
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => location.reload());
});

/**
 * Convertit une couleur RGB en RGBA avec une transparence.
 * @param {string} color - Couleur rgb(...)
 * @param {number} alpha - Transparence (0 à 1)
 * @returns {string} - Couleur rgba(...)
 */
function hexToRgba(color, alpha) {
    return color.replace('rgb', 'rgba').replace(')', `, ${alpha})`);
}

/**
 * Retourne l’unité associée à une métrique selon son index.
 * @param {number} index - Index du dataset
 * @returns {string} Unité correspondante
 */
function getUnit(index) {
    return ['°C', '%', 'hPa', 'mm'][index] || '';
}
