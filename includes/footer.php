
<footer class="mt-5 pt-4 pb-4 bg-light text-center text-muted" id="pied" style="margin-bottom: 0px">
    <div class="container">
        <p class=" ">Site web interactif de consultation météorologique</p>
        <p class=" ">Données mises à jour automatiquement — Prévisions fiables sur 7 jours</p>
        <p class=" ">Conçu pour offrir une expérience utilisateur claire, moderne et intuitive</p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="index.php" class="text-decoration-none text-muted">Accueil</a>
            <a href="previsions.php" class="text-decoration-none text-muted">Prévisions</a>
            <a href="historiqueglobal.php" class="text-decoration-none text-muted">Historique</a> 
            <a href="contact.php" class="text-decoration-none text-muted">Contact</a>
            <a href="/apod/index.php" class="text-decoration-none text-muted">Page tech</a>
        </div>

        <p class="mt-3 small">© 2025 Tous droits réservés — Dernière mise à jour : avril 2025</p>
    </div>
</footer>

<script>
//<![CDATA[
    const pied = document.getElementById('pied');
    const boutonMode = document.getElementById('bouton-mode');
    if (boutonMode) {
        boutonMode.addEventListener('click', function() {
            pied.classList.toggle('bg-light');
            pied.classList.toggle('bg-dark');
            pied.classList.toggle('text-muted');
            pied.classList.toggle('text-light');
        });
    }
//]]>
</script>
</body>
</html>