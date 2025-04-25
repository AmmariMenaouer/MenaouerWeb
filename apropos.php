<?php
session_start();
include 'includes/head.inc.php';

include 'includes/header.inc.php';
include 'includes/lang.php';
?>

<main class="container  ">

  <!-- Bouton retour -->
  <div class="  text-start">
    <a href="index.php" class="btn btn-outline-primary" style="background-color: white; margin-top: 30px">
       <?= $t['home'] ?>
    </a>
  </div>

  <h1 class="text-center  " style="color: white;margin-bottom: 30px"> <?= $t['title_apropos'] ?></h1>

  <!-- Introduction -->
  <section class=" " style="margin-bottom: 2px; padding-bottom: 5px;">
    <div class="card shadow p-4">
      <h2 class="h5  "><?= $t['section_objectif'] ?></h2>
      <p><?= $t['text_objectif'] ?>
       </p>
    
  

  <!-- Fonctionnalités -->
  
      <h2 class="h5  "> <?= $t['section_fonctionnalites'] ?></h2>
      <ul>
        <li><?= $t['feature_search'] ?></li>
        <li> <?= $t['feature_forecast'] ?></li>
        <li> <?= $t['feature_charts'] ?></li>
        <li> <?= $t['feature_map'] ?></li>
        <li> <?= $t['feature_history_csv'] ?></li>
        <li> <?= $t['feature_stats_graph'] ?></li>
        <li> <?= $t['feature_darkmode'] ?></li>
        <li> <?= $t['feature_structure'] ?></li>
      </ul>
  

  <!-- Technologies -->
  
      <h2 class="h5  "> <?= $t['section_technologies'] ?></h2>
      <ul>
      <li><?= $t['tech_php'] ?></li>
      <li><?= $t['tech_js'] ?></li>
      <li><?= $t['tech_front'] ?></li>
      <li><?= $t['tech_api_weather'] ?></li>
      <li><?= $t['tech_api_geo'] ?></li>

      </ul>
  

  <!-- Développeurs -->
  
      <h2 class="h5  "> <?= $t['section_developpeurs'] ?></h2>
      <p>
      <?= $t['texte_developpeurs'] ?>
      </p>
      
    </div>
  </section>

  <!-- Remerciements -->
  <section>
    <div class="text-center">
      <p class="text-muted"> <?= $t['merci'] ?> </p>
    </div>
  </section>

</main>

<!-- Flèche retour accueil -->
<a href="index.php" class="btn-retour" title="Retour à l'accueil">&#x1F3E0;</a>

<?php include 'includes/footer.php'; ?>
