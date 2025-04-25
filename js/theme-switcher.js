/**
 * @fileoverview Gère les interactions liées au thème (clair/sombre), 
 * au changement de langue, et à l'effet de scroll de la navbar.
 * Applique les préférences stockées dans le `localStorage` et adapte l'interface en conséquence.
 */

/**
 * S'exécute une fois que le DOM est entièrement chargé.
 * - Applique le thème sombre si stocké en local.
 * - Active le switch thème clair/sombre.
 * - Gère le changement de langue par redirection.
 * - Ajoute un effet visuel à la navbar au scroll.
 *
 * @event DOMContentLoaded
 * @returns {void}
 * @author groupe 7
 */
document.addEventListener("DOMContentLoaded", function () {
    const bouton = document.getElementById('bouton-theme');      // Bouton pour basculer le thème
    const icone = document.getElementById('icone-theme');        // Icône dans le bouton thème
    const corps = document.body;                                 // Corps du document
    const selectLangue = document.getElementById('select-langue'); // Menu déroulant pour changer de langue

    //  Appliquer le thème stocké dans le localStorage
    if (localStorage.getItem("theme") === "dark") {
        corps.classList.add("dark-mode");
        icone.classList.replace("fa-moon", "fa-sun");
    }

    /**
     * Toggle le thème clair/sombre au clic sur le bouton.
     * Met à jour l'icône et stocke la préférence dans le localStorage.
     */
    bouton.addEventListener("click", () => {
        const isDark = corps.classList.toggle("dark-mode");
        icone.classList.replace(isDark ? "fa-moon" : "fa-sun", isDark ? "fa-sun" : "fa-moon");
        localStorage.setItem("theme", isDark ? "dark" : "light");
    });

    /**
     * Redirige vers l'URL correspondant à la langue sélectionnée.
     * La valeur de l'option du select doit être une URL complète ou relative.
     */
    selectLangue.addEventListener("change", function () {
        window.location.href = this.value;
    });

    /**
     * Ajoute une classe à la navbar lors du scroll pour appliquer un effet visuel (ex. : fond flouté, ombre).
     */
    window.addEventListener("scroll", function () {
        const nav = document.getElementById("navbar");
        if (window.scrollY > 10) {
            nav.classList.add("scrolled");
        } else {
            nav.classList.remove("scrolled");
        }
    });
});
