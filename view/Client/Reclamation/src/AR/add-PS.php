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
            $success = "&#1578;&#1605; &#1573;&#1590;&#1575;&#1601;&#1577; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609; &#1576;&#1606;&#1580;&#1575;&#1581;!";
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
    <title>&#1573;&#1590;&#1575;&#1601;&#1577; &#1588;&#1603;&#1608;&#1609;</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
    <style>
        /* Styles spécifiques pour l'arabe */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header h1, .header p, 
        .form-label, .btn,
        .alert strong, .footer strong {
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
    </style>
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128640; &#1573;&#1590;&#1575;&#1601;&#1577; &#1588;&#1603;&#1608;&#1609; &#1580;&#1583;&#1610;&#1583;&#1577;</h1>
        <p>&#1605;&#1608;&#1590;&#1608;&#1593;: &#1605;&#1588;&#1603;&#1604;&#1577; &#1571;&#1605;&#1606;&#1610;&#1577;</p>
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
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- SUJET (READONLY) -->
            <div class="form-group">
                <label class="form-label">&#1575;&#1604;&#1605;&#1608;&#1590;&#1608;&#1593;:</label>
                <input type="text" name="sujet" value="&#1605;&#1588;&#1603;&#1604;&#1577;_&#1571;&#1605;&#1606;&#1610;&#1577;" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">&#1575;&#1604;&#1608;&#1589;&#1601;:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="&#1589;&#1616;&#1601; &#1575;&#1604;&#1605;&#1588;&#1603;&#1604;&#1577; &#1575;&#1604;&#1571;&#1605;&#1606;&#1610;&#1577; &#1575;&#1604;&#1578;&#1610; &#1608;&#1575;&#1580;&#1607;&#1578;&#1607;&#1575;..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 500 &#1581;&#1585;&#1601;
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">&#1575;&#1604;&#1576;&#1585;&#1610;&#1583; &#1575;&#1604;&#1573;&#1604;&#1603;&#1578;&#1585;&#1608;&#1606;&#1610;:</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="example@email.com">
            </div>

            <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128228; &#1573;&#1585;&#1587;&#1575;&#1604; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609;</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; &#1573;&#1604;&#1594;&#1575;&#1569;</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; &#1605;&#1604;&#1575;&#1581;&#1592;&#1577;:</strong> 
            &#1610;&#1578;&#1605; &#1575;&#1604;&#1578;&#1593;&#1575;&#1605;&#1604; &#1605;&#1593; &#1575;&#1604;&#1605;&#1588;&#1575;&#1603;&#1604; &#1575;&#1604;&#1571;&#1605;&#1606;&#1610;&#1577; &#1576;&#1571;&#1593;&#1604;&#1609; &#1571;&#1608;&#1604;&#1608;&#1610;&#1577;.
            &#1587;&#1578;&#1578;&#1604;&#1602;&#1609; &#1578;&#1571;&#1603;&#1610;&#1583;&#1611;&#1575; &#1576;&#1575;&#1604;&#1575;&#1587;&#1578;&#1604;&#1575;&#1605; &#1582;&#1604;&#1575;&#1604; 24 &#1587;&#1575;&#1593;&#1577;.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&#1605;&#1588;&#1594;&#1604; &#1576;&#1608;&#1575;&#1587;&#1591;&#1577; <strong>&#1578;&#1608;&#1606;&#1610;&#1587;&#1576;&#1610;&#1587; &#1580;&#1575;&#1604;&#1575;&#1603;&#1587;&#1610;</strong> | &#1575;&#1604;&#1571;&#1605;&#1606; &#1608;&#1575;&#1604;&#1582;&#1589;&#1608;&#1589;&#1610;&#1577;</p>
    </footer>
</div>

<script>
    // &#1593;&#1583;&#1575;&#1583; &#1575;&#1604;&#1571;&#1581;&#1585;&#1601; &#1604;&#1604;&#1608;&#1589;&#1601;
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
    
    // &#1578;&#1607;&#1610;&#1574;&#1577; &#1575;&#1604;&#1593;&#1583;&#1575;&#1583;
    charCount.textContent = textarea.value.length;
    
    // &#1578;&#1571;&#1603;&#1610;&#1583; &#1602;&#1576;&#1604; &#1575;&#1604;&#1573;&#1585;&#1587;&#1575;&#1604;
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!confirm("&#1607;&#1604; &#1578;&#1572;&#1603;&#1583; &#1573;&#1585;&#1587;&#1575;&#1604; &#1607;&#1584;&#1607; &#1575;&#1604;&#1588;&#1603;&#1608;&#1609; &#1575;&#1604;&#1571;&#1605;&#1606;&#1610;&#1577;&#1567;")) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>