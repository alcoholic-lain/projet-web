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
    <title>Report Technical Problem</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128295; Technical Problem</h1>
        <p>Report a malfunction or technical bug</p>
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
                <label class="form-label">Problem Type:</label>
                <input type="text" name="sujet" value="Technical problem" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">Detailed Description:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="Describe the technical problem encountered (steps to reproduce, error message, browser used, etc.)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 characters
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">Your Email (for follow-up):</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="example@email.com">
            </div>

          
            <!-- BUTTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128295; Submit Technical Report</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; Cancel</span>
                </a>
            </div>
        </form>

        <!-- ADDITIONAL INFO -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; Note:</strong> 
            Our technical team will review your report as soon as possible.
            You will receive an update by email once the problem is analyzed.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Powered by <strong>Tunispace Galaxy</strong> | Technical Support</p>
    </footer>
</div>

<script>
    // Character counter
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('charCount');
    
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
    
    // Loading animation on submission
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });
</script>

</body>
</html>