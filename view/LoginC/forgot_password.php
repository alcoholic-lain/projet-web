<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié • Space</title>
    <link rel="stylesheet" href="assets/css/forgotpsw.css">
</head>
<body>
<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<div class="forgot-container">
    <div class="forgot-card">
        <h1 class="title">Mot de passe oublié</h1>
        <p class="subtitle">Entre ton adresse email</p>

        <form id="forgotForm">
            <input type="email" name="email" placeholder="ton@email.com" >
            <button type="submit"name="btenv">Envoyer le lien</button>
            <p id="message" style="margin-top:15px; font-weight:bold;"></p>
        </form>

        <a href="login.html" id="btg">Retour connexion</a>
    </div>
</div>

<script src="assets/js/forgotpsw.js"></script>
</body>
</html>