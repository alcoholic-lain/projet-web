<?php
include_once __DIR__ . '../../../../../../config.php';
include_once __DIR__ . '../../../../../../model/Reclamtion/reclam.php';
include_once __DIR__ . '../../../../../../controller/components/Reclamtion/ReclamationController.php';

// Fonction d'envoi de notification email
function envoyerNotificationUrgente($sujet, $description, $email_utilisateur) {
    $to = "ayarimed50@gmail.com";
    $subject = "&#128680; &#1578;&#1605; &#1575;&#1603;&#1578;&#1588;&#1575;&#1601; &#1588;&#1603;&#1608;&#1609; &#1593;&#1575;&#1580;&#1604;&#1577;";
    
    $message = "
    <html>
    <head>
        <title>&#1573;&#1588;&#1593;&#1575;&#1585; &#1588;&#1603;&#1608;&#1609; &#1593;&#1575;&#1580;&#1604;&#1577;</title>
        <style>
            body { font-family: Arial, sans-serif; direction: rtl; text-align: right; }
            .urgent { color: #FF0000; font-weight: bold; }
            .info { background-color: #f8f9fa; padding: 10px; border-left: 4px solid #dc3545; }
        </style>
    </head>
    <body>
        <h2 class='urgent'>&#9888;&#65039; &#1588;&#1603;&#1608;&#1609; &#1593;&#1575;&#1580;&#1604;&#1577;</h2>
        <div class='info'>
            <p><strong>&#1575;&#1604;&#1605;&#1608;&#1590;&#1608;&#1593;:</strong> " . htmlspecialchars($sujet) . "</p>
            <p><strong>&#1575;&#1604;&#1608;&#1589;&#1601;:</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>
            <p><strong>&#1575;&#1604;&#1605;&#1587;&#1578;&#1582;&#1583;&#1605;:</strong> " . htmlspecialchars($email_utilisateur) . "</p>
            <p><strong>&#1575;&#1604;&#1578;&#1575;&#1585;&#1610;&#1582;:</strong> " . date('d/m/Y H:i:s') . "</p>
        </div>
        <p>&#1578;&#1605; &#1608;&#1590;&#1593; &#1593;&#1604;&#1575;&#1605;&#1577; &#1593;&#1604;&#1609; &#1607;&#1584;&#1607; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609; &#1603;&#1593;&#1575;&#1580;&#1604;&#1577; &#1605;&#1606; &#1602;&#1576;&#1604; &#1575;&#1604;&#1605;&#1587;&#1578;&#1582;&#1583;&#1605;.</p>
        <hr>
        <p><small>&#1607;&#1584;&#1575; &#1573;&#1588;&#1593;&#1575;&#1585; &#1578;&#1604;&#1602;&#1575;&#1574;&#1610;&#1548; &#1575;&#1604;&#1585;&#1580;&#1575;&#1569; &#1593;&#1583;&#1605; &#1575;&#1604;&#1585;&#1583;.</small></p>
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
            $success = "&#1578;&#1605; &#1573;&#1590;&#1575;&#1601;&#1577; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609; &#1576;&#1606;&#1580;&#1575;&#1581;!";
            
            // Si la réclamation est marquée comme urgente, envoyer une notification
            if($urgent) {
                if(envoyerNotificationUrgente($sujet, $description, $user)) {
                    $success .= " &#1578;&#1605; &#1573;&#1585;&#1587;&#1575;&#1604; &#1573;&#1588;&#1593;&#1575;&#1585; &#1593;&#1575;&#1580;&#1604; &#1573;&#1604;&#1609; &#1575;&#1604;&#1605;&#1587;&#1572;&#1608;&#1604;.";
                    $notification_envoyee = true;
                } else {
                    $success .= " (&#1605;&#1604;&#1575;&#1581;&#1592;&#1577;: &#1604;&#1605; &#1610;&#1578;&#1605;&#1603;&#1606; &#1605;&#1606; &#1573;&#1585;&#1587;&#1575;&#1604; &#1575;&#1604;&#1573;&#1588;&#1593;&#1575;&#1585; &#1575;&#1604;&#1593;&#1575;&#1580;&#1604;)";
                }
            }
            
            // Réinitialiser le formulaire
            $_POST = array();
        } else {
            $error = "&#1582;&#1591;&#1571; &#1571;&#1579;&#1606;&#1575;&#1569; &#1573;&#1590;&#1575;&#1601;&#1577; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609;";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>&#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1605;&#1581;&#1578;&#1608;&#1609; &#1594;&#1610;&#1585; &#1604;&#1575;&#1574;&#1602;</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header h1, .header p, 
        .form-label, .btn,
        .alert strong, .footer strong,
        .notification-header h3,
        .toggle-title, .toggle-desc,
        .urgent-warning {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-input, .form-textarea {
            text-align: right;
            direction: rtl;
        }
        
        .char-counter {
            text-align: left;
        }
        
        .button-group {
            flex-direction: row-reverse;
        }
        
        .toggle-container {
            flex-direction: row-reverse;
        }
        
        .toggle-checkbox {
            margin-right: 0;
            margin-left: 20px;
        }
        
        .urgent-checkbox label {
            flex-direction: row-reverse;
        }
        
        .urgent-checkbox input[type=checkbox] {
            margin-right: 0;
            margin-left: 10px;
        }
        
        .urgent-note {
            padding-left: 0;
            padding-right: 30px;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128680; &#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1605;&#1581;&#1578;&#1608;&#1609;</h1>
        <p>&#1605;&#1608;&#1590;&#1608;&#1593;: &#1605;&#1581;&#1578;&#1608;&#1609; &#1594;&#1610;&#1585; &#1589;&#1581;&#1610;&#1581; &#1571;&#1608; &#1594;&#1610;&#1585; &#1604;&#1575;&#1574;&#1602;</p>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <?php if($error): ?>
            <div class="alert alert-error">
                <strong>&#9888;&#65039; &#1582;&#1591;&#1571;:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <strong>&#9989; &#1578;&#1605; &#1576;&#1606;&#1580;&#1575;&#1581;!</strong> <?php echo htmlspecialchars($success); ?>
                <?php if($notification_envoyee): ?>
                    <div style="margin-top: 10px; padding: 10px; background: rgba(255, 0, 0, 0.1); border-radius: 5px; border-right: 4px solid #ff0000;">
                        <span style="color: #ff0000; font-weight: bold;">&#9888;&#65039; &#1578;&#1605; &#1573;&#1585;&#1587;&#1575;&#1604; &#1575;&#1604;&#1573;&#1588;&#1593;&#1575;&#1585; &#1573;&#1604;&#1609; &#1575;&#1604;&#1605;&#1587;&#1572;&#1608;&#1604;</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUJET (READONLY) -->
            <div class="form-group">
                <label class="form-label">&#1575;&#1604;&#1605;&#1608;&#1590;&#1608;&#1593;:</label>
                <input type="text" name="sujet" value="&#1605;&#1581;&#1578;&#1608;&#1609; &#1594;&#1610;&#1585; &#1589;&#1581;&#1610;&#1581;" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">&#1608;&#1589;&#1601; &#1605;&#1601;&#1589;&#1604;:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="&#1589;&#1616;&#1601; &#1575;&#1604;&#1605;&#1581;&#1578;&#1608;&#1609; &#1575;&#1604;&#1605;&#1588;&#1603;&#1604;&#1577; &#1576;&#1575;&#1604;&#1578;&#1601;&#1589;&#1610;&#1604; (&#1575;&#1604;&#1585;&#1575;&#1576;&#1591;&#1548; &#1604;&#1602;&#1591;&#1577; &#1575;&#1604;&#1588;&#1575;&#1588;&#1577;&#1548; &#1587;&#1576;&#1576; &#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594;)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 &#1581;&#1585;&#1601;
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">&#1576;&#1585;&#1610;&#1583;&#1603; &#1575;&#1604;&#1573;&#1604;&#1603;&#1578;&#1585;&#1608;&#1606;&#1610;:</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="example@email.com">
            </div>

           
            <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <span>&#128680; &#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1575;&#1604;&#1605;&#1581;&#1578;&#1608;&#1609;</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; &#1573;&#1604;&#1594;&#1575;&#1569;</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; &#1605;&#1604;&#1575;&#1581;&#1592;&#1577; &#1607;&#1575;&#1605;&#1577;:</strong> 
            &#1610;&#1578;&#1605; &#1575;&#1604;&#1578;&#1593;&#1575;&#1605;&#1604; &#1605;&#1593; &#1580;&#1605;&#1610;&#1593; &#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594;&#1575;&#1578; &#1576;&#1587;&#1585;&#1610;&#1577; &#1578;&#1575;&#1605;&#1577;.
            &#1606;&#1581;&#1606; &#1605;&#1604;&#1578;&#1586;&#1605;&#1608;&#1606; &#1576;&#1605;&#1585;&#1575;&#1580;&#1593;&#1577; &#1603;&#1604; &#1581;&#1575;&#1604;&#1577; &#1601;&#1610; &#1571;&#1602;&#1585;&#1576; &#1608;&#1602;&#1578; &#1605;&#1605;&#1603;&#1606;.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&#1605;&#1588;&#1594;&#1604; &#1576;&#1608;&#1575;&#1587;&#1591;&#1577; <strong>&#1578;&#1608;&#1606;&#1610;&#1587;&#1576;&#1610;&#1587; &#1580;&#1575;&#1604;&#1575;&#1603;&#1587;&#1610;</strong> | &#1605;&#1585;&#1575;&#1602;&#1576;&#1577; &#1575;&#1604;&#1605;&#1581;&#1578;&#1608;&#1609;</p>
    </footer>
</div>

<script>
    // &#1593;&#1583;&#1575;&#1583; &#1575;&#1604;&#1571;&#1581;&#1585;&#1601;
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('charCount');
    const urgentToggle = document.getElementById('urgentToggle');
    const urgentCheckbox = document.getElementById('urgentCheckbox');
    const urgentWarning = document.getElementById('urgentWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    // &#1593;&#1583;&#1575;&#1583; &#1575;&#1604;&#1571;&#1581;&#1585;&#1601;
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
    
    // &#1578;&#1607;&#1610;&#1574;&#1577; &#1575;&#1604;&#1593;&#1583;&#1575;&#1583;
    charCount.textContent = textarea.value.length;
    
    // &#1573;&#1583;&#1575;&#1585;&#1577; &#1578;&#1576;&#1583;&#1610;&#1604; &#1575;&#1604;&#1593;&#1575;&#1580;&#1604;&#1577;
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
            submitBtn.innerHTML = '<span>&#128680; &#1573;&#1585;&#1587;&#1575;&#1604; &#1575;&#1604;&#1578;&#1606;&#1576;&#1610;&#1607; &#1575;&#1604;&#1593;&#1575;&#1580;&#1604;</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
        } else {
            urgentToggle.classList.remove('active');
            urgentWarning.style.display = 'none';
            submitBtn.innerHTML = '<span>&#128680; &#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1575;&#1604;&#1605;&#1581;&#1578;&#1608;&#1609;</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        }
    }
    
    // &#1578;&#1607;&#1610;&#1574;&#1577; &#1608;&#1575;&#1580;&#1607;&#1577; &#1575;&#1604;&#1605;&#1587;&#1578;&#1582;&#1583;&#1605;
    updateUrgentUI();
    
    // &#1578;&#1571;&#1603;&#1610;&#1583; &#1604;&#1604;&#1581;&#1575;&#1604;&#1575;&#1578; &#1575;&#1604;&#1593;&#1575;&#1580;&#1604;&#1577;
    document.querySelector('form').addEventListener('submit', function(e) {
        if (urgentCheckbox.checked) {
            if (!confirm("&#128680; &#1578;&#1606;&#1576;&#1610;&#1607; - &#1581;&#1575;&#1604;&#1577; &#1593;&#1575;&#1580;&#1604;&#1577;\n\n&#1571;&#1606;&#1578; &#1593;&#1604;&#1609; &#1608;&#1588;&#1603; &#1573;&#1585;&#1587;&#1575;&#1604; &#1578;&#1606;&#1576;&#1610;&#1607; &#1593;&#1575;&#1580;&#1604;.\n\n&#1587;&#1610;&#1578;&#1605; &#1606;&#1602;&#1604; &#1607;&#1584;&#1575; &#1575;&#1604;&#1578;&#1606;&#1576;&#1610;&#1607; &#1593;&#1604;&#1609; &#1575;&#1604;&#1601;&#1608;&#1585; &#1573;&#1604;&#1609; &#1575;&#1604;&#1605;&#1587;&#1572;&#1608;&#1604;.\n\n&#1607;&#1604; &#1578;&#1572;&#1603;&#1583; &#1571;&#1606; &#1607;&#1584;&#1575; &#1575;&#1604;&#1605;&#1581;&#1578;&#1608;&#1609; &#1610;&#1578;&#1591;&#1604;&#1576; &#1578;&#1583;&#1582;&#1604;&#1575;&#1611; &#1601;&#1608;&#1585;&#1610;&#1575;&#1611;&#1567;")) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm("&#1607;&#1604; &#1578;&#1572;&#1603;&#1583; &#1573;&#1585;&#1587;&#1575;&#1604; &#1607;&#1584;&#1575; &#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594;&#1567;")) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>

</body>
</html>