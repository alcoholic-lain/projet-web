<?php
require_once __DIR__ . '/../../../../../config.php';
require_once __DIR__ . '/../../../../../model/Reclamtion/reclam.php';
require_once __DIR__ . '/../../../../../controller/components/Reclamtion/ReclamationController.php';

$error = "";
$success = "";

if($_POST) {

    $user = $_POST['user'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';

    // Validation
    $errors = validateReclamation($user, $sujet, $description);

    if(empty($errors)) {
        $controller = new ReclamationController();

        if($controller->addReclamation($user, $sujet, $description, $statut)) {
            $success = "Réclamation ajoutée avec succès!";
            $_POST = array(); // reset form
        } else {
            $error = "Erreur lors de l'ajout de la réclamation";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Réclamation</title>
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>

<header>
    <h1>🚀 Ajouter une Réclamation</h1>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">Nouvelle réclamation</h2>
    </div>

    <section style="width: 60%; margin: 0 auto;">

        <?php if($error): ?>
            <div class="alert error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <!-- User -->
            <div>
                <label>Utilisateur :</label>
                <input type="text" name="user" required
                       value="<?= $_POST['user'] ?? '' ?>">
            </div>

            <!-- Sujet -->
            <div>
                <label>Sujet :</label>
                <input type="text" name="sujet" value="Probleme_de_securite" readonly>
            </div>

            <!-- Description -->
            <div>
                <label>Description :</label>
                <textarea name="description" required rows="5"><?= $_POST['description'] ?? '' ?></textarea>
            </div>

            <!-- Mail -->
            <div>
                <label>Email :</label>
                <input type="email" name="mail" required
                       value="<?= $_POST['mail'] ?? '' ?>">
            </div>

            <input type="hidden" name="statut" value="en attente">

            <div style="text-align:center;">
                <button type="submit" class="valider">Ajouter la réclamation</button>
                <a href="../choix.php" class="rejeter">Annuler</a>
            </div>

        </form>

    </section>
</main>

<footer>
    <p>Powered by <span>Tunispace Galaxy</span></p>
</footer>

</body>
</html>
