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
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128640; Ajouter une Réclamation</h1>
        <p>Sujet : Problème de Sécurité</p>
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
                <strong>&#9989; Succès !</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUJET (READONLY) -->
            <div class="form-group">
                <label class="form-label">Sujet :</label>
                <input type="text" name="sujet" value="Probleme_de_securité" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Description :</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Décrivez le problème de sécurité rencontré..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 500 caractères
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Email :</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="votre@email.com">
            </div>

            <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128228; Envoyer la Réclamation</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; Annuler</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; Note :</strong> Les problèmes de sécurité sont traités avec la plus haute priorité. 
            Un accusé de réception vous sera envoyé dans les 24 heures.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Sécurité & Confidentialité</p>
    </footer>
</div>

<script>
    // Compteur de caractères pour la description
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
    
    // Confirmation avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!confirm("Confirmez-vous l'envoi de cette réclamation de sécurité ?")) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>