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
    <title>&#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1605;&#1588;&#1603;&#1604;&#1577; &#1578;&#1602;&#1606;&#1610;&#1577;</title>
    <link rel="stylesheet" href="../../assets/css/rec.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header h1, .header p, 
        .form-label, .btn,
        .alert strong, .footer strong,
        .notification-header h3 {
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
        
        .email-display > div {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- HEADER -->
    <header class="header">
        <h1>&#128295; &#1605;&#1588;&#1603;&#1604;&#1577; &#1578;&#1602;&#1606;&#1610;&#1577;</h1>
        <p>&#1575;&#1604;&#1573;&#1576;&#1604;&#1575;&#1594; &#1593;&#1606; &#1593;&#1591;&#1604; &#1571;&#1608; &#1582;&#1591;&#1571; &#1578;&#1602;&#1606;&#1610;</p>
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
                <label class="form-label">&#1606;&#1608;&#1593; &#1575;&#1604;&#1605;&#1588;&#1603;&#1604;&#1577;:</label>
                <input type="text" name="sujet" value="&#1605;&#1588;&#1603;&#1604;&#1577; &#1578;&#1602;&#1606;&#1610;&#1577;" readonly class="form-input">
            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">
                <label class="form-label">&#1608;&#1589;&#1601; &#1605;&#1601;&#1589;&#1604;:</label>
                <textarea name="description" required rows="5" class="form-textarea" placeholder="&#1589;&#1616;&#1601; &#1575;&#1604;&#1605;&#1588;&#1603;&#1604;&#1577; &#1575;&#1604;&#1578;&#1602;&#1606;&#1610;&#1577; &#1575;&#1604;&#1578;&#1610; &#1608;&#1575;&#1580;&#1607;&#1578;&#1607;&#1575; (&#1582;&#1591;&#1608;&#1575;&#1578; &#1573;&#1593;&#1575;&#1583;&#1577; &#1575;&#1604;&#1578;&#1603;&#1585;&#1575;&#1585;&#1548; &#1585;&#1587;&#1575;&#1604;&#1577; &#1575;&#1604;&#1582;&#1591;&#1571;&#1548; &#1575;&#1604;&#1605;&#1578;&#1589;&#1601;&#1581; &#1575;&#1604;&#1605;&#1587;&#1578;&#1582;&#1583;&#1605;&#1548; &#1573;&#1604;&#1582;)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 &#1581;&#1585;&#1601;
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">&#1576;&#1585;&#1610;&#1583;&#1603; &#1575;&#1604;&#1573;&#1604;&#1603;&#1578;&#1585;&#1608;&#1606;&#1610; (&#1604;&#1604;&#1605;&#1578;&#1575;&#1576;&#1593;&#1577;):</label>
                <input type="email" name="user" value="<?php echo htmlspecialchars($_POST['mail'] ?? $_POST['user'] ?? ''); ?>" required class="form-input" placeholder="example@email.com">
            </div>

                        <!-- BOUTONS -->
            <div class="button-group">
                <button type="submit" class="btn btn-submit">
                    <span>&#128295; &#1573;&#1585;&#1587;&#1575;&#1604; &#1575;&#1604;&#1578;&#1602;&#1585;&#1610;&#1585; &#1575;&#1604;&#1578;&#1602;&#1606;&#1610;</span>
                </button>
                <a href="../choix.php" class="btn btn-cancel">
                    <span>&#10060; &#1573;&#1604;&#1594;&#1575;&#1569;</span>
                </a>
            </div>
        </form>

        <!-- INFO SUPPLEMENTAIRE -->
        <div class="alert alert-info">
            <strong>&#8505;&#65039; &#1605;&#1604;&#1575;&#1581;&#1592;&#1577;:</strong> 
            &#1587;&#1610;&#1602;&#1608;&#1605; &#1601;&#1585;&#1610;&#1602;&#1606;&#1575; &#1575;&#1604;&#1601;&#1606;&#1610; &#1576;&#1605;&#1585;&#1575;&#1580;&#1593;&#1577; &#1578;&#1602;&#1585;&#1610;&#1585;&#1603; &#1601;&#1610; &#1571;&#1602;&#1585;&#1576; &#1608;&#1602;&#1578;.
            &#1587;&#1578;&#1578;&#1604;&#1602;&#1609; &#1578;&#1581;&#1583;&#1610;&#1579;&#1611;&#1575; &#1593;&#1576;&#1585; &#1575;&#1604;&#1576;&#1585;&#1610;&#1583; &#1575;&#1604;&#1573;&#1604;&#1603;&#1578;&#1585;&#1608;&#1606;&#1610; &#1576;&#1605;&#1580;&#1585;&#1583; &#1578;&#1581;&#1604;&#1610;&#1604; &#1575;&#1604;&#1605;&#1588;&#1603;&#1604;&#1577;.
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&#1605;&#1588;&#1594;&#1604; &#1576;&#1608;&#1575;&#1587;&#1591;&#1577; <strong>&#1578;&#1608;&#1606;&#1610;&#1587;&#1576;&#1610;&#1587; &#1580;&#1575;&#1604;&#1575;&#1603;&#1587;&#1610;</strong> | &#1575;&#1604;&#1583;&#1593;&#1605; &#1575;&#1604;&#1601;&#1606;&#1610;</p>
    </footer>
</div>

<script>
    // &#1593;&#1583;&#1575;&#1583; &#1575;&#1604;&#1571;&#1581;&#1585;&#1601;
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
    
    // &#1578;&#1607;&#1610;&#1574;&#1577; &#1575;&#1604;&#1593;&#1583;&#1575;&#1583;
    charCount.textContent = textarea.value.length;
    
    // &#1578;&#1581;&#1605;&#1610;&#1604; &#1575;&#1604;&#1585;&#1587;&#1608;&#1605; &#1575;&#1604;&#1605;&#1578;&#1581;&#1585;&#1603;&#1577; &#1593;&#1606;&#1583; &#1575;&#1604;&#1573;&#1585;&#1587;&#1575;&#1604;
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });
</script>

</body>
</html>