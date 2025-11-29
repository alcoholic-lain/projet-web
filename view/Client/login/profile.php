<?php
session_start();
require_once '../../../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$db = config::getConnexion();
$stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

// Avatar par défaut si aucun avatar existant
$avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/img/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
<div class="profile-wrapper">
    <h1 class="title">✨ Mon Profil</h1>

    <div class="profile-card">
        <!-- Avatar -->
        <div class="avatar-section">
            <img
                    id="user-avatar"
                    class="avatar"
                    src="/projet-web/<?= $avatar ?>"
                    alt="Avatar utilisateur"
            >
            <form id="avatarForm" enctype="multipart/form-data">
                <label class="upload-btn">
                    Changer l’image
                    <input type="file" name="avatar" accept="image/*" hidden>
                </label>
                <button class="btn-save" type="submit">Enregistrer</button>
            </form>
        </div>

        <!-- Infos utilisateur -->
        <form id="profileForm" class="info-section">
            <label>Pseudo</label>
            <input type="text" name="pseudo" value="<?= htmlspecialchars($user['pseudo']) ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

            <button class="btn-save" type="submit">💾 Enregistrer les modifications</button>
        </form>

        <!-- Mot de passe -->
        <div class="password-section">
            <h2>Changer le mot de passe</h2>
            <form id="passwordForm">
                <input type="password" id="currentPassword" name="currentPassword" placeholder="Mot de passe actuel">
                <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe">
                <button class="btn-save" type="submit">🔐 Modifier</button>
            </form>
        </div>

        <button class="btn-logout" onclick="window.location.href='logout.php'">🚪 Se Déconnecter</button>
    </div>
</div>

<script src="assets/js/profile.js"></script>
</body>
</html>
