<?php
include_once __DIR__ . '../../../../../../config.php';
include_once __DIR__ . '../../../../../../model/Reclamtion/reclam.php';
include_once __DIR__ . '../../../../../../controller/components/Reclamtion/ReclamationController.php';

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
            $success = "RÃ©clamation ajoutÃ©e avec succÃ¨s!";
            // RÃ©initialiser le formulaire
            $_POST = array();
        } else {
            $error = "Erreur lors de l'ajout de la rÃ©clamation";
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
    <title>Ajouter une RÃ©clamation</title>
    <link rel="stylesheet" href="../../assets/css/add.css">
</head>
<body>

<header>
    <h1>&#128640; Add a Claim</h1>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">New claim</h2>
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
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Username:</label>
                <input type="text" name="user" value="<?php echo $_POST['user'] ?? ''; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="margin-bottom: 20px;">

                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Subject:</label>
                <input type="text" name="sujet" value="Compte bloqu�" readonly
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description:</label>
                <textarea name="description" required rows="5"
                          style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Email:</label>
                <input type="text" name="user" value="<?php echo $_POST['mail'] ?? ''; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--purple); color: white;">
            </div>

            <div style="text-align: center;">
                <button type="submit" class="valider">Add claim</button>
                <a href="../choix.php" class="rejeter" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </section>
</main>

<footer>
    <p>Powered by �
	<span style="color: rgb(215, 218, 252); font-family: Inter, sans-serif; font-size: 18.4px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: center; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial; display: inline !important; float: none; background-color: rgba(10, 12, 26, 0.4)">
	&nbsp;</span><span style="margin: 0px; padding: 0px; box-sizing: border-box; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / 0.5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; border-width: 0px; border-style: solid; border-color: rgb(229, 231, 235); background: linear-gradient(90deg, rgb(255, 127, 42), rgb(255, 42, 127), rgb(164, 107, 255)) text rgba(10, 12, 26, 0.4); color: transparent; font-weight: 600; text-shadow: rgba(255, 127, 42, 0.4) 0px 0px 18px; font-family: Inter, sans-serif; font-size: 18.4px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; letter-spacing: normal; orphans: 2; text-align: center; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;">Tunispace 
	Galaxy</span></p>
</footer>

</body>
</html>