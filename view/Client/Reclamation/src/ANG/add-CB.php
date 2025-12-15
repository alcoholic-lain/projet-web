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
    $statut = $_POST['statut'] ?? 'pending';
    
    // Validation
    $errors = validateReclamation($user, $sujet, $description);
    
    if(empty($errors)) {
        $controller = new ReclamationController();
        if($controller->addReclamation($user, $sujet, $description, $statut)) {
            $success = "Claim added successfully!";
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
    <title>Add a Claim</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128640; Add a New Claim</h1>
        <p>Subject: Blocked Account</p>
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
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUBJECT (READONLY) -->
            <div class="form-group">
                <label class="form-label">Subject:</label>
                <input type="text" name="sujet" value="Blocked account" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Description:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Describe why your account was blocked and provide any relevant information..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 500 characters
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Email associated with the account:</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="email@example.com">
            </div>

            <!-- BUTTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128275; Request Unblock</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; Cancel</span>
                </a>
            </div>
        </form>

        <!-- ADDITIONAL INFO -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; Note:</strong> 
            Account unblock requests are processed within 48 business hours.
            Please provide as many details as possible to expedite processing.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Account Support</p>
    </footer>
</div>

<script>
    // Character counter for description
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
    
    // Initialize counter
    charCount.textContent = textarea.value.length;
</script>

</body>
</html>