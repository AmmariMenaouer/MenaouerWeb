<script>
  document.addEventListener("DOMContentLoaded", () => {
    const theme = localStorage.getItem("theme");
    if (theme === "dark") {
      document.body.classList.add("dark-mode");
    }
  });

  const boutonMode = document.getElementById("bouton-mode");
  if (boutonMode) {
    boutonMode.addEventListener("click", () => {
      document.body.classList.toggle("dark-mode");
      const modeActuel = document.body.classList.contains("dark-mode") ? "dark" : "light";
      localStorage.setItem("theme", modeActuel);
    });
  }
</script>

