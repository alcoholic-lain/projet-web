<?php
session_start();
require_once '../../config.php'; // adapte le chemin si nécessaire

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$db = config::getConnexion();

// Récupérer l'utilisateur connecté
$stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si jamais l'utilisateur n'existe pas
if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="profile-container">
    <h1 class="title">👤 Profil </h1>

    <div class="profile-card">

        <!-- Informations utilisateur -->
        <div class="profile-info">
            <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/img/avatar.png') ?>"
                 alt="Photo de Profil"
                 class="avatar">

            <div class="profile-details">
                <p><strong>Nom :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>

        <!-- Changement de mot de passe -->
        <div class="change-password">
            <h2 class="subtitle">🔑 Changer le mot de passe</h2>

            <form id="changePasswordForm">
                <input type="password" id="currentPassword" placeholder="Mot de passe actuel" required>
                <input type="password" id="newPassword" placeholder="Nouveau mot de passe" required>
                <input type="password" id="confirmPassword" placeholder="Confirmer le mot de passe" required>

                <button type="submit" class="btn-change">Modifier le mot de passe</button>
                <p id="message"></p>
            </form>
        </div>

        <!-- Déconnexion -->
        <div class="profile-actions">
            <button class="btn-logout" onclick="window.location.href='login.html'">Se Déconnecter</button>
        </div>

    </div>
</div>

<script src="assets/js/profile.js"></script>
</body>
</html>
