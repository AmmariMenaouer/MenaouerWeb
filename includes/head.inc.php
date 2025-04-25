<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/lang.php';

if (isset($_GET['theme'])) {
    $theme = $_GET['theme'];
    if (in_array($theme, ['light', 'dark'])) {
        $_SESSION['theme'] = $theme;
    }
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $url");
    exit;
}
$theme = $_SESSION['theme'] ?? 'light';

if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $translations)) {
    $_SESSION['lang'] = $_GET['lang'];
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    parse_str($_SERVER['QUERY_STRING'], $params);
    unset($params['lang']);
    $newQuery = http_build_query($params);
    if (!empty($newQuery)) {
        $url .= '?' . $newQuery;
    }
    header("Location: $url");
    exit;
}
$lang = $_SESSION['lang'] ?? 'fr';
$t = $translations[$lang] ?? $translations['fr'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $t['title'] ?></title>
  <link rel="icon" href="../images/favicon.png" type="image/png" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body, .navbar {
      transition: background-color 0.5s ease, color 0.5s ease, backdrop-filter 0.5s ease;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      font-size: 1rem;
      background-color: #f0faff;
      color: #1a1a1a;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
    }

    body.dark-mode {
      background-color: #121212;
      color: #ffffff;
    }

    .navbar {
  background: linear-gradient(90deg, #d0eaff, #ffe3f0); /* Bleu clair → Rose doux */
  backdrop-filter: blur(10px) saturate(180%);
  -webkit-backdrop-filter: blur(10px) saturate(180%);
  border-bottom-left-radius: 1.5rem;
  border-bottom-right-radius: 1.5rem;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 1000;
  transition: all 0.3s ease-in-out;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.navbar a {
  color: #111; 
  font-weight: 600;
  font-size: 1.1rem;
  text-decoration: none;
  margin: 0 1.5rem;
  transition: color 0.3s ease-in-out;
}

.navbar a:hover {
  color: #cc2b5e;
}

.navbar:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  transform: translateY(-1px);
}


.navbar:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.navbar:hover {
  box-shadow: 0 10px 32px rgba(0, 0, 0, 0.4);
  transform: translateY(-2px);
}



    body.dark-mode .navbar {
      background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(50, 50, 50, 0.95));
      box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
    }

    .navbar-brand span {
      font-size: 1.5rem;
      font-weight: bold;
      color: #ffffff;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    .navbar .nav-link {
      font-weight: 500;
      color: #003366;
      transition: all 0.3s ease-in-out;
    }

    .navbar .nav-link:hover {
      color: #ff9900;
    }

    body.dark-mode .navbar .nav-link {
      color: #ffffff !important;
    }

    body.dark-mode .navbar .nav-link:hover {
      color: #ffc107 !important;
    }

    .navbar .nav-link i {
      font-size: 1.2rem;
      margin-right: 0.5em;
      transition: transform 0.3s ease, color 0.3s ease;
      color: #0d6efd;
    }

    .navbar .nav-link:hover i {
      transform: scale(1.2);
      color: #ff9900;
    }

    .form-select {
      border-radius: 25px;
      padding: 0.4rem 1rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .switch-theme {
      position: relative;
      display: inline-block;
      width: 70px;
      height: 35px;
      margin-left: 15px;
    }

    .switch-theme input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      background-color: #3399ff;
      border-radius: 35px;
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      transition: 0.4s;
    }

    .slider::before {
      position: absolute;
      content: "";
      height: 27px;
      width: 27px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      border-radius: 50%;
      transition: 0.4s;
    }

    .switch-theme input:checked + .slider::before {
      transform: translateX(35px);
    }

    .slider .icon {
      position: absolute;
      top: 7px;
      left: 10px;
      width: 20px;
      height: 20px;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      z-index: 1;
      filter: drop-shadow(0 0 2px rgba(0,0,0,0.5));
    }

    .switch-theme input:checked + .slider .icon {
      left: 40px;
      background-image: url('https://img.icons8.com/ios-filled/50/ffffff/moon-symbol.png');
    }

    .switch-theme input:not(:checked) + .slider .icon {
      background-image: url('https://img.icons8.com/ios-filled/50/000000/sun--v1.png');
    }
  
        .modal-dialog { max-width: 100%; margin: auto; }
        .modal-content { max-height: 100%; overflow-y: auto; }

        #contenuHeures-wrapper {
            position: relative;
            overflow-x: auto;
            white-space: nowrap;
            padding: 1rem;
        }
        #contenuHeures {
            display: flex;
            gap: 1rem;
            scroll-snap-type: x mandatory;
        }
        #contenuHeures > div {
            min-width: 160px;
            background-color: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 12px;
            text-align: center;
            scroll-snap-align: center;
        }
        .bouton-scroll {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: #ffffffdd;
            border: none;
            border-radius: 50%;
            padding: 8px 12px;
            cursor: pointer;
            z-index: 1056;
        }
        #btn-gauche { left: 5px; }
        #btn-droite { right: 5px; }

        body.dark-mode #contenuHeures > div {
            background-color: #1e1e1e;
            color: #f1f1f1;
        }
        body.dark-mode .bouton-scroll {
            background-color: #333333dd;
            color: #ffffff;
        }
        body.dark-mode .modal-content {
            background-color: #2c2c2c;
            color: #f5f5f5;
        }
.btn-retour {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background-color: #0d6efd;
  color: white;
  padding: 0.8rem 1rem;
  border-radius: 50%;
  text-decoration: none;
  font-size: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: transform 0.2s ease;
  z-index: 1000;
}
.btn-retour:hover {
  transform: scale(1.1);
  background-color: #084298;
}

/* Section formulaire */
section.selection-region {
    background-color: var(--fond-clair);
    padding: 50px 40px;
    max-width: 900px;
    margin: 60px auto;
    border-radius: 1.5rem;
    box-shadow: 0 12px 32px var(--ombre);
    color: var(--texte);
    backdrop-filter: blur(6px);
    transition: all 0.4s ease;
}

section.selection-region h2 {
    text-align: center;
    margin-bottom: 35px;
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--texte);
}

form.selection-departement,
form.selection-ville {
    margin-bottom: 35px;
    animation: fadeIn 0.6s ease;
}

.label-select {
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 1.1rem;
    display: block;
}

.select-input {
    width: 100%;
    padding: 14px 18px;
    font-size: 1.05rem;
    border-radius: 12px;
    border: 2px solid var(--bordure);
    background-color: var(--carte);
    color: var(--texte);
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    transition: 0.3s ease all;
}
.select-input:focus {
    border-color: var(--primaire);
    outline: none;
    box-shadow: 0 0 10px rgba(13,110,253,0.15);
    background-color: #fff;
}

.btn-valider {
    background: linear-gradient(135deg, var(--primaire), #0b5ed7);
    color: #fff;
    padding: 14px 22px;
    font-size: 1.1rem;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}
.btn-valider:hover {
    background: linear-gradient(135deg, var(--temp), #b02a37);
    transform: translateY(-2px) scale(1.02);
}

.retour-lien {
    display: block;
    margin-top: 30px;
    text-align: center;
    font-size: 1rem;
    font-weight: 500;
    color: var(--primaire);
    text-decoration: none;
    transition: 0.3s ease;
}
.retour-lien:hover {
    text-decoration: underline;
    color: var(--temp);
}

@media (max-width: 768px) {
    section.selection-region {
        padding: 30px 20px;
    }

    section.selection-region h2 {
        font-size: 1.6rem;
    }

    .select-input,
    .btn-valider {
        font-size: 1rem;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
