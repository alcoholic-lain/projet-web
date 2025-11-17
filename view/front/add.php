<?php
include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../controller/ReclamationController.php';

$error = "";
$success = "";

if($_POST) {
    $user = $_POST['user'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';
    
    // Validation
    $errors = validateReclamation($user, $sujet, $description);
    
    if(empty($errors)) {
        $controller = new ReclamationController();
        if($controller->addReclamation($user, $sujet, $description, $statut)) {
            $success = "Réclamation ajoutée avec succès!";
            // Réinitialiser le formulaire
            $_POST = array();
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
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <h1>➕ Ajouter une Réclamation</h1>
	<nav>
        Reclamation</nav>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">Nouvelle réclamation</h2>
    </div>

    <section style="width: 60%; margin: 0 auto;">
        <?php if($error): ?>
            <div style="background: #FF6B6B; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div style="background: #4AFF8B; color: #0B0E26; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Utilisateur:</label>
                <input type="text" name="user" value="<?php echo $_POST['user'] ?? ''; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Sujet:</label>
                <input type="text" name="sujet" value="<?php echo $_POST['sujet'] ?? ''; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description:</label>
                <textarea name="description" required rows="5"
                          style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Mail:</label>
                <input type="text" name="user" value="<?php echo $_POST['mail'] ?? ''; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="text-align: center;">
                <button type="submit" class="valider">Ajouter la réclamation</button>
                <a href="choix.php" class="rejeter" style="margin-left: 10px;">Annuler</a>
            </div>
        </form>
    </section>
</main>

<footer>
    <p>Powered by TEC_MAX 2025 © Version 1.0✨</p>
</footer>

</body>
</html>