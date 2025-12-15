<?php
include_once __DIR__ . '../../../../../../config.php';
include_once __DIR__ . '../../../../../../model/Reclamtion/reclam.php';
include_once __DIR__ . '../../../../../../controller/components/Reclamtion/ReclamationController.php';

// Fonction d'envoi de notification email
function envoyerNotificationUrgente($sujet, $description, $email_utilisateur) {
    $to = "ayarimed50@gmail.com";
    $subject = "🚨 RÉCLAMATION URGENTE DÉTECTÉE";
    
    $message = "
    <html>
    <head>
        <title>Notification Réclamation Urgente</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .urgent { color: #FF0000; font-weight: bold; }
            .info { background-color: #f8f9fa; padding: 10px; border-left: 4px solid #dc3545; }
        </style>
    </head>
    <body>
        <h2 class='urgent'>⚠️ RÉCLAMATION URGENTE</h2>
        <div class='info'>
            <p><strong>Sujet :</strong> " . htmlspecialchars($sujet) . "</p>
            <p><strong>Description :</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>
            <p><strong>Utilisateur :</strong> " . htmlspecialchars($email_utilisateur) . "</p>
            <p><strong>Date :</strong> " . date('d/m/Y H:i:s') . "</p>
        </div>
        <p>Cette réclamation a été marquée comme urgente par l'utilisateur.</p>
        <hr>
        <p><small>Ceci est une notification automatique, merci de ne pas répondre.</small></p>
    </body>
    </html>
    ";
    
    // Headers pour email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: systeme-reclamations@votredomaine.com" . "\r\n";
    $headers .= "X-Priority: 1 (Highest)" . "\r\n";
    $headers .= "X-MSMail-Priority: High" . "\r\n";
    $headers .= "Importance: High" . "\r\n";
    
    // Envoi de l'email
    return mail($to, $subject, $message, $headers);
}

$error = "";
$success = "";
$notification_envoyee = false;

if($_POST) {
    $user = $_POST['user'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';
    $urgent = isset($_POST['urgent']) ? 1 : 0;
    
    // Validation
    $errors = validateReclamation($user, $sujet, $description);
    
    if(empty($errors)) {
        $controller = new ReclamationController();
        
        // Ajouter le champ urgent dans l'appel
        if($controller->addReclamation($user, $sujet, $description, $statut, $urgent)) {
            $success = "Réclamation ajoutée avec succès!";
            
            // Si la réclamation est marquée comme urgente, envoyer une notification
            if($urgent) {
                if(envoyerNotificationUrgente($sujet, $description, $user)) {
                    $success .= " Une notification d'urgence a été envoyée à l'administrateur.";
                    $notification_envoyee = true;
                } else {
                    $success .= " (Note : La notification d'urgence n'a pas pu être envoyée)";
                }
            }
            
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
    <title>Signalement de Contenu Inapproprié</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>🚨 Signalement de Contenu</h1>
        <p>Sujet : Contenu Incorrect ou Inapproprié</p>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <?php if($error): ?>
            <div class="alert alert-error">
                <strong>⚠️ Erreur :</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <strong>✅ Succès !</strong> <?php echo htmlspecialchars($success); ?>
                <?php if($notification_envoyee): ?>
                    <div style="margin-top: 10px; padding: 10px; background: rgba(255, 0, 0, 0.1); border-radius: 5px; border-left: 4px solid #ff0000;">
                        <span style="color: #ff0000; font-weight: bold;">⚠️ Notification envoyée à l'administrateur</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUJET (READONLY) -->
            <div class="form-group">
                <label class="form-label">Sujet :</label>
                <input type="text" name="sujet" value="Contenu incorrect" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Description détaillée :</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Décrivez le contenu problématique en détail (URL, capture d'écran, raison du signalement)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 caractères
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Votre Email :</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="votre@email.com">
            </div>

            <!-- OPTION URGENT -->
            <div class="notification-section">
                <div class="notification-header">
                    <h3>🚨 Option Urgence</h3>
                </div>
                
                <div class="toggle-wrapper">
                    <label class="toggle-container" id="urgentToggle">
                        <input type="checkbox" name="urgent" value="1" class="toggle-checkbox" id="urgentCheckbox">
                        <div class="toggle-label">
                            <span class="toggle-title">
                                Marquer comme URGENT
                                <span class="notification-badge">ALERTE</span>
                            </span>
                            <span class="toggle-desc">
                                Une notification immédiate sera envoyée à l'administrateur.
                                À utiliser uniquement pour les contenus dangereux, illégaux ou extrêmement préjudiciables.
                            </span>
                        </div>
                    </label>
                    
                    <div class="urgent-warning" id="urgentWarning" style="display: none;">
                        <strong>⚠️ ATTENTION :</strong> Cette option ne doit être utilisée que pour les cas critiques nécessitant une intervention immédiate. Un abus de cette fonctionnalité peut entraîner la suspension de votre compte.
                    </div>
                </div>
            </div>

            <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <span>🚨 Signaler le Contenu</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>❌ Annuler</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>ℹ️ Note importante :</strong> 
            Tous les signalements sont traités avec confidentialité. 
            Nous nous engageons à examiner chaque cas dans les plus brefs délais.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Modération de Contenu</p>
    </footer>
</div>

<script>
    // Compteur de caractères
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('charCount');
    const urgentToggle = document.getElementById('urgentToggle');
    const urgentCheckbox = document.getElementById('urgentCheckbox');
    const urgentWarning = document.getElementById('urgentWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    // Compteur de caractères
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 900) {
            charCount.style.color = '#ff6b6b';
        } else if (length > 800) {
            charCount.style.color = '#ffd166';
        } else {
            charCount.style.color = '#94a3b8';
        }
    });
    
    // Initialiser le compteur
    charCount.textContent = textarea.value.length;
    
    // Gestion du toggle urgent
    urgentToggle.addEventListener('click', function(e) {
        if (e.target !== urgentCheckbox) {
            urgentCheckbox.checked = !urgentCheckbox.checked;
        }
        
        updateUrgentUI();
    });
    
    urgentCheckbox.addEventListener('change', updateUrgentUI);
    
    function updateUrgentUI() {
        if (urgentCheckbox.checked) {
            urgentToggle.classList.add('active');
            urgentWarning.style.display = 'block';
            submitBtn.innerHTML = '<span>🚨 ENVOYER L\'ALERTE URGENTE</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
        } else {
            urgentToggle.classList.remove('active');
            urgentWarning.style.display = 'none';
            submitBtn.innerHTML = '<span>🚨 Signaler le Contenu</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        }
    }
    
    // Initialiser l'UI
    updateUrgentUI();
    
    // Confirmation pour les urgences
    document.querySelector('form').addEventListener('submit', function(e) {
        if (urgentCheckbox.checked) {
            if (!confirm("🚨 ATTENTION - ALERTE URGENTE\n\nVous êtes sur le point d'envoyer une alerte urgente.\n\nCette alerte sera immédiatement transmise à l'administrateur.\n\nConfirmez-vous que ce contenu nécessite une intervention immédiate ?")) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm("Confirmez-vous l'envoi de ce signalement ?")) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>

</body>
</html>