<?php
session_start();
include 'includes/lang.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$confirmation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim(htmlspecialchars($_POST['nom'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    if ($nom && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $to = "menaouerammari@gmail.com";
        $sujet = "📬 Nouveau message depuis le site météo";
        $contenu = "Vous avez reçu un message depuis le formulaire de contact :\n\n";
        $contenu .= "Nom : $nom\n";
        $contenu .= "Email : $email\n";
        $contenu .= "Message :\n$message\n";

        $headers = "From: \"Site Météo\" <no-reply@tonsite.com>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $sujet, $contenu, $headers)) {
            $confirmation = "&#x2705; Merci $nom ! Votre message a bien été envoyé.";
        } else {
            $confirmation = "&#x274C; Une erreur est survenue lors de l'envoi du message.";
        }
    } else {
        $confirmation = "&#x274C; Merci de remplir tous les champs correctement.";
    }
}
include 'includes/head.inc.php';

include 'includes/header.inc.php';
?>


<main class="container  ">
    <div class="  text-start">
        <a href="index.php" class="btn btn-outline-primary" style="background-color: white; margin-top: 30px">
Retour à l’accueil
        </a>
    </div>

    <h1 class="text-center  " style="color:white"><?= $t['contactus']?> </h1>

    <?php if ($confirmation): ?>
        <div class="alert alert-info text-center"><?= $confirmation ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="contact.php" class="card shadow p-4">
                <div class=" ">
                    <label for="nom" class="form-label"><?= $t['name']?></label>
                    <input type="text" name="nom" id="nom" class="form-control" required="required" />
                </div>
                <div class=" ">
                    <label for="email" class="form-label"><?= $t['mail']?></label>
                    <input type="email" name="email" id="email" class="form-control" required="required" />
                </div>
                <div class=" ">
                    <label for="message" class="form-label"><?= $t['message']?></label>
                    <textarea name="message" id="message" rows="5" class="form-control" required="required"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Envoyer le message</button>
            </form>
        </div>
    </div>
</main>

<a href="index.php" class="btn-retour" title="Retour à l'accueil">&#x1F3E0;</a>

<?php include 'includes/footer.php'; ?>
