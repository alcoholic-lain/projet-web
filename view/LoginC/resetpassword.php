<?php require_once "../../config.php"; session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe • Space</title>
    <link rel="stylesheet" href="assets/css/resetpassword.css">
</head>
<body>

<div class="forgot-container">
    <div class="forgot-card">

        <?php
        $token = $_GET['token'] ?? '';
        if (strlen($token) !== 64) {
            echo "<p style='color:red;text-align:center;font-weight:bold;'>Lien invalide ou corrompu.</p>"; exit;
        }

        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
        $stmt->execute([$token]);
        $row = $stmt->fetch();

        if (!$row) {
            echo "<p style='color:red;text-align:center;font-weight:bold;'>Lien expiré ou déjà utilisé.</p>";
            echo "<a href='forgot_password.php' style='display:block;text-align:center;margin-top:20px;'>← Nouveau lien</a>";
            exit;
        }
        $email = $row['email'];
        $success = false;

        if ($_POST) {
            $new     = $_POST['newPassword'] ?? '';
            $confirm = $_POST['confirmPassword'] ?? '';
            $errors  = [];

            // Ancien mot de passe
            $stmtOld = $db->prepare("SELECT password FROM user WHERE email = ?");
            $stmtOld->execute([$email]);
            $oldHash = $stmtOld->fetchColumn();

            if ($oldHash && password_verify($new, $oldHash)) {
                $errors[] = "Le nouveau mot de passe ne doit pas être identique à l'ancien";
            }

            // Vérifications classiques
            if (strlen($new) < 8) $errors[] = "8 caractères minimum";
            if (!preg_match('/[A-Z]/', $new)) $errors[] = "1 majuscule requise";
            if (!preg_match('/[a-z]/', $new)) $errors[] = "1 minuscule requise";
            if (!preg_match('/[0-9]/', $new)) $errors[] = "1 chiffre requis";
            if (!preg_match('/[^A-Za-z0-9]/', $new)) $errors[] = "1 symbole requis (!@#$%^&*...)";
            if ($new !== $confirm) $errors[] = "Les mots de passe ne correspondent pas";

            if (empty($errors)) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $db->prepare("UPDATE user SET password = ? WHERE email = ?")->execute([$hash, $email]);
                $db->prepare("DELETE FROM password_resets WHERE token = ?")->execute([$token]);
                $success = true;
            } else {
                foreach ($errors as $err) {
                    echo "<div style='color:red;text-align:center;margin:10px 0;'>$err</div>";
                }
            }
        }
        ?>

        <h1 class="title">Nouveau mot de passe</h1>
        <p class="subtitle">Pour le compte : <strong><?php echo htmlspecialchars($email); ?></strong></p>

        <?php if($success): ?>
            <div style="text-align:center; padding:20px; color:white; background: #4caf50; border-radius:10px; font-weight:bold;">
                Mot de passe changé avec succès !<br>
                Redirection vers la page de connexion...
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 3000); // redirige après 3 secondes
            </script>
        <?php else: ?>
            <form method="POST" novalidate id="resetForm">
                <input type="password" name="newPassword" id="newPassword" placeholder="Nouveau mot de passe" required>
                <div class="msg" id="newPasswordMsg"></div>

                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirmer le mot de passe" required>
                <div class="msg" id="confirmPasswordMsg"></div>

                <button type="submit" id="v1">Valider le changement</button>
            </form>
        <?php endif; ?>

    </div>
</div>

<!-- Validation dynamique JS -->
<script src="assets/js/resetpassword-validation.js"></script>

</body>
</html>
