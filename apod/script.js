// Fonction pour basculer le thème
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    // Mettre à jour l'attribut data-theme
    document.documentElement.setAttribute('data-theme', newTheme);

    // Mettre à jour le cookie
    document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`;

    // Mettre à jour l'URL sans recharger la page
    const url = new URL(window.location.href);
    url.searchParams.set('theme', newTheme);
    window.history.pushState({}, '', url);

    // Mettre à jour l'icône et le titre du bouton
    const themeIcon = document.querySelector('.theme-toggle i');
    themeIcon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    document.querySelector('.theme-toggle').title = newTheme === 'dark' ? document.querySelector('.theme-toggle').getAttribute('data-light-text') : document.querySelector('.theme-toggle').getAttribute('data-dark-text');
}

// Initialisation du thème au chargement
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        themeToggle.innerHTML = `<i class="fas fa-${currentTheme === 'dark' ? 'sun' : 'moon'}"></i>`;
        themeToggle.setAttribute('title', currentTheme === 'dark' ? themeToggle.getAttribute('data-light-text') : themeToggle.getAttribute('data-dark-text'));
    }
});