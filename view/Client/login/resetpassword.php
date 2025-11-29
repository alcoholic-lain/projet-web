<?php
// resetpassword.php
// Ce fichier doit être ouvert via Apache : http://localhost/...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="assets/css/resetpassword.css">
</head>
<body>
<div class="reset-container">
    <div class="reset-card">
        <h1 class="title">🔑 Réinitialiser le mot de passe</h1>

        <form id="resetPasswordForm">
            <!-- Champ caché pour stocker le token -->
            <input type="hidden" id="token" value="<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES); ?>">

            <div class="input-group">
                <label for="newPassword">Nouveau mot de passe</label>
                <input type="password" id="newPassword" >
            </div>

            <div class="input-group">
                <label for="confirmPassword">Confirmer le mot de passe</label>
                <input type="password" id="confirmPassword" >
            </div>

            <button type="submit" class="btn-reset">Réinitialiser</button>
            <p id="message"></p>
        </form>

        <div class="login-link">
            <a href="login.html">⬅ Retour à la connexion</a>
        </div>
    </div>
</div>

<script src="assets/js/resetpassword.js"></script>
</body>
</html>
