<?php
include_once __DIR__ . '../../../../../../config.php';
include_once __DIR__ . '../../../../../../model/Reclamtion/reclam.php';
include_once __DIR__ . '../../../../../../controller/components/Reclamtion/ReclamationController.php';

// Email notification function
function sendUrgentNotification($subject, $description, $user_email) {
    $to = "ayarimed50@gmail.com";
    $subject = "&#128680; URGENT CLAIM DETECTED";
    
    $message = "
    <html>
    <head>
        <title>Urgent Claim Notification</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .urgent { color: #FF0000; font-weight: bold; }
            .info { background-color: #f8f9fa; padding: 10px; border-left: 4px solid #dc3545; }
        </style>
    </head>
    <body>
        <h2 class='urgent'>&#9888;&#65039; URGENT CLAIM</h2>
        <div class='info'>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>
            <p><strong>User:</strong> " . htmlspecialchars($user_email) . "</p>
            <p><strong>Date:</strong> " . date('d/m/Y H:i:s') . "</p>
        </div>
        <p>This claim has been marked as urgent by the user.</p>
        <hr>
        <p><small>This is an automatic notification, please do not reply.</small></p>
    </body>
    </html>
    ";
    
    // Headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: system-claims@yourdomain.com" . "\r\n";
    $headers .= "X-Priority: 1 (Highest)" . "\r\n";
    $headers .= "X-MSMail-Priority: High" . "\r\n";
    $headers .= "Importance: High" . "\r\n";
    
    // Send the email
    return mail($to, $subject, $message, $headers);
}

$error = "";
$success = "";
$notification_sent = false;

if($_POST) {
    $user = $_POST['user'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'pending';
    $urgent = isset($_POST['urgent']) ? 1 : 0;
    
    // Validation
    $errors = validateReclamation($user, $sujet, $description);
    
    if(empty($errors)) {
        $controller = new ReclamationController();
        
        // Add urgent field to the call
        if($controller->addReclamation($user, $sujet, $description, $statut, $urgent)) {
            $success = "Claim added successfully!";
            
            // If claim is marked as urgent, send notification
            if($urgent) {
                if(sendUrgentNotification($sujet, $description, $user)) {
                    $success .= " An urgent notification has been sent to the administrator.";
                    $notification_sent = true;
                } else {
                    $success .= " (Note: The urgent notification could not be sent)";
                }
            }
            
            // Reset the form
            $_POST = array();
        } else {
            $error = "Error while adding the claim";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Inappropriate Content</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128680; Content Report</h1>
        <p>Subject: Incorrect or Inappropriate Content</p>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <?php if($error): ?>
            <div class="alert alert-error">
                <strong>&#9888;&#65039; Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <strong>&#9989; Success!</strong> <?php echo htmlspecialchars($success); ?>
                <?php if($notification_sent): ?>
                    <div style="margin-top: 10px; padding: 10px; background: rgba(255, 0, 0, 0.1); border-radius: 5px; border-left: 4px solid #ff0000;">
                        <span style="color: #ff0000; font-weight: bold;">&#9888;&#65039; Notification sent to administrator</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUBJECT (READONLY) -->
            <div class="form-group">
                <label class="form-label">Subject:</label>
                <input type="text" name="sujet" value="Incorrect content" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Detailed Description:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Describe the problematic content in detail (URL, screenshot, reason for reporting)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 characters
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Your Email:</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="example@email.com">
            </div>


            <!-- BUTTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <span>&#128680; Report Content</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; Cancel</span>
                </a>
            </div>
        </form>

        <!-- ADDITIONAL INFO -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; Important Note:</strong> 
            All reports are handled confidentially.
            We are committed to reviewing each case as soon as possible.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Content Moderation</p>
    </footer>
</div>

<script>
    // Character counter
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('charCount');
    const urgentToggle = document.getElementById('urgentToggle');
    const urgentCheckbox = document.getElementById('urgentCheckbox');
    const urgentWarning = document.getElementById('urgentWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    // Character counter
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
    
    // Initialize counter
    charCount.textContent = textarea.value.length;
    
    // Urgent toggle management
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
            submitBtn.innerHTML = '<span>&#128680; SEND URGENT ALERT</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
        } else {
            urgentToggle.classList.remove('active');
            urgentWarning.style.display = 'none';
            submitBtn.innerHTML = '<span>&#128680; Report Content</span>';
            submitBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        }
    }
    
    // Initialize UI
    updateUrgentUI();
    
    // Confirmation for urgent cases
    document.querySelector('form').addEventListener('submit', function(e) {
        if (urgentCheckbox.checked) {
            if (!confirm("&#128680; WARNING - URGENT ALERT\n\nYou are about to send an urgent alert.\n\nThis alert will be immediately forwarded to the administrator.\n\nDo you confirm that this content requires immediate intervention?")) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm("Do you confirm sending this report?")) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>

</body>
</html>
