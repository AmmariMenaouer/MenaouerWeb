</head>

<body class="<?= $theme === 'dark' ? 'dark-mode' : '' ?>">
<header>
  <nav class="navbar navbar-expand-lg navbar-dark py-3 shadow">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <img src="../images/logo.jpeg" alt="Logo" style="height: 40px; border-radius: 50%;">
        <span class="fw-bold"><?= $t['title'] ?></span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav me-auto   mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> <?= $t['home'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="carte.php"><i class="fas fa-map"></i> <?= $t['card_map'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="previsions.php"><i class="fas fa-cloud-sun-rain"></i> <?= $t['forecast'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="statistiques.php"><i class="fas fa-area-chart"></i> <?= $t['statistics'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="historiqueglobal.php"><i class="fa fa-history"></i> <?= $t['history'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="apropos.php"><i class="fas fa-info-circle"></i> <?= $t['about'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> <?= $t['contact'] ?></a></li>

        </ul>

        <form class="d-flex align-items-center me-3" method="get"  style="margin-right: 20px;">
          <select name="lang" class="form-select" onchange="this.form.submit()">
            <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>&#x1F1EB;&#x1F1F7; Français</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>&#x1F1EC;&#x1F1E7; English</option>
          </select>
        </form>

        <label class="switch-theme">
          <input type="checkbox" id="themeCheckbox" <?= $theme === 'dark' ? 'checked' : '' ?>>
          <span class="slider"><span class="icon" id="switchIcon"></span></span>
        </label>
      </div>
    </div>
  </nav>
</header>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const checkbox = document.getElementById('themeCheckbox');
    const isDark = "<?= $theme ?>" === "dark";

    const dayImages = [
      'images/ciel.jpg',
      'images/cielclair.jpg',
      'images/cielnuageux.jpg'
    ];

    const nightImages = [
      'images/etoiles.jpg', 
      'images/nuit2.jpg'
    ];

    const images = isDark ? nightImages : dayImages;
    const image = images[Math.floor(Math.random() * images.length)];
    document.body.style.backgroundImage = `url('${image}')`;

    checkbox.addEventListener('change', () => {
      const newTheme = checkbox.checked ? 'dark' : 'light';
      localStorage.setItem('theme', newTheme);
      window.location.href = "?theme=" + newTheme;
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  