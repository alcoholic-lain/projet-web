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
            $success = "Reclamation ajoutee avec succes!";
            // Reinitialiser le formulaire
            $_POST = array();
        } else {
            $error = "Erreur lors de l'ajout de la reclamation";
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
    <title>Ajouter une Reclamation</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128640; Ajouter une Reclamation</h1>
        <p>Sujet : Compte Bloque</p>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <?php if($error): ?>
            <div class="alert alert-error">
                <strong>&#9888;&#65039; Erreur :</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <strong>&#9989; Succes !</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUJET (READONLY) -->
            <div class="form-group">
                <label class="form-label">Sujet :</label>
                <input type="text" name="sujet" value="Compte bloque" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Description :</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Decrivez pourquoi votre compte a ete bloque et fournissez toute information pertinente..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 500 caracteres
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Email associe au compte :</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="email@ducompte.com">
            </div>

            <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128275; Demander le Deblocage</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; Annuler</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; Note :</strong> Les demandes de deblocage de compte sont traitees sous 48 heures ouvrables. 
            Veuillez fournir le maximum de details pour accelerer le traitement.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Support Compte</p>
    </footer>
</div>

<script>
    // Compteur de caracteres pour la description
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 450) {
            charCount.style.color = '#ff6b6b';
        } else if (length > 400) {
            charCount.style.color = '#ffd166';
        } else {
            charCount.style.color = '#94a3b8';
        }
    });
    
    // Initialiser le compteur
    charCount.textContent = textarea.value.length;
</script>

</body>
</html>