document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const bouton = document.getElementById('bouton-theme');
    const icone = document.getElementById('icone-theme');

    // Vérifiez le thème actuel dans le localStorage
    let theme = localStorage.getItem("theme");

    // Si aucun thème n'est défini, détectez le thème préféré de l'utilisateur
    if (!theme) {
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        localStorage.setItem("theme", theme);
    }

    // Appliquez le thème enregistré
    if (theme === "dark") {
        body.classList.add("dark-mode");
        icone.classList.replace("fa-moon", "fa-sun");
    } else {
        body.classList.remove("dark-mode");
        icone.classList.replace("fa-sun", "fa-moon");
    }

    // Basculer entre les thèmes au clic
    bouton.addEventListener("click", () => {
        const isDark = body.classList.toggle("dark-mode");
        localStorage.setItem("theme", isDark ? "dark" : "light");

        // Changez l'icône en fonction du thème
        if (isDark) {
            icone.classList.replace("fa-moon", "fa-sun");
        } else {
            icone.classList.replace("fa-sun", "fa-moon");
        }
    });
});