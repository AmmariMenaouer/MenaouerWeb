<?php
session_start();
require_once 'functions_meteo_Api/enregistrementhistorique.php';

// GESTION DES FORMULAIRES AVANT TOUT AFFICHAGE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['supprimer'])) {
        $ville = filter_input(INPUT_POST, 'supprimer', FILTER_SANITIZE_STRING);
        if ($ville) {
            supprimerHistorique($ville);
        }
    } elseif (isset($_POST['tout_supprimer'])) {
        supprimerToutHistorique();
    }

    // Redirection après traitement
    header("Location: statistiques.php");
    exit;
}

// Inclure header APRÈS le traitement pour éviter le "headers already sent"
include 'includes/head.inc.php';
?>
<style>
    :root {
        --bg-color: #ffffff;
        --text-color: #000000;
        --table-header-bg: #007BFF;
        --table-header-color: #ffffff;
        --table-border: #ddd;
        --button-bg: crimson;
        --button-hover: darkred;
        --btn-clear-bg: #555;
        --btn-clear-hover: #333;
        --link-color: #007BFF;
    }

    body.dark-mode {
        --bg-color: #1e1e1e;
        --text-color: #f0f0f0;
        --table-header-bg: #333;
        --table-header-color: #f0f0f0;
        --table-border: #444;
        --button-bg: #cc0000;
        --button-hover: #990000;
        --btn-clear-bg: #444;
        --btn-clear-hover: #222;
        --link-color: #4da3ff;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--bg-color);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-top: 20px;
        color: var(--text-color);
    }

    th, td {
        padding: 16px;
        border: 1px solid var(--table-border);
        text-align: center;
        font-size: 16px;
    }

    th {
        background-color: var(--table-header-bg);
        color: var(--table-header-color);
    }

    button {
        background-color: var(--button-bg);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: var(--button-hover);
    }

    .center {
        text-align: center;
        margin-top: 20px;
        color: var(--text-color);
    }

    .btn-clear {
        background-color: var(--btn-clear-bg);
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
        cursor: pointer;
        border: none;
    }

    .btn-clear:hover {
        background-color: var(--btn-clear-hover);
    }

    a {
        color: var(--link-color);
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<?php
include 'includes/header.inc.php';
$historique = lireHistorique();
?>



<h1 class="text-center mb-4" style="color:white; margin-top: 30px;">
    <?= $t['history'] ?? 'Historique des recherches' ?>
</h1>

<section style="background-color: var(--bg-color); border-radius: 30px; padding: 30px; margin: 100px auto; max-width: 1200px; color: var(--text-color);">
    <?php if (empty($historique)) : ?>
        <p class="center">Aucune recherche enregistrée pour le moment.</p>
    <?php else : ?>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Date</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $ligne) :
                        $ville = htmlspecialchars($ligne[0] ?? '');
                        $date  = htmlspecialchars($ligne[1] ?? '');
                        $lat   = htmlspecialchars($ligne[2] ?? '');
                        $lon   = htmlspecialchars($ligne[3] ?? '');
                    ?>
                        <tr>
                            <td>
                                <a href="index.php?ville=<?= urlencode($ville) ?>" title="Revoir la météo">
                                    <?= $ville ?>
                                </a>
                            </td>
                            <td><?= $date ?></td>
                            <td><?= $lat ?></td>
                            <td><?= $lon ?></td>
                            <td>
                                <button type="submit" name="supprimer" value="<?= $ville ?>" onclick="return confirm('Supprimer cette entrée ?')">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <div class="center">
            <form method="post">
                <button type="submit" name="tout_supprimer" class="btn-clear" onclick="return confirm('Supprimer tout l’historique ?')">
                    Supprimer tout l’historique
                </button>
            </form>
        </div>
    <?php endif; ?>

    <div class="center mt-4">
        <a href="index.php" class="btn-clear">Retour à l’accueil</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
