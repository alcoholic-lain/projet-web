<?php
session_start();
require_once '../../config.php';
require_once '../../protect.php'; // Vérifie que l'utilisateur est connecté

$db = config::getConnexion();

// Récupérer toutes les infos de l'utilisateur
$stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../LoginC/login.html');
    exit;
}

// Lire le template HTML F.html

$html = file_get_contents(__DIR__ . '/F.html');


// Remplacer les placeholders par les infos réelles
$html = str_replace('{{AVATAR}}', htmlspecialchars($user['avatar'] ?? 'assets/img/default-avatar.png'), $html);
$html = str_replace('{{USERNAME}}', htmlspecialchars($user['pseudo']), $html);
$html = str_replace('{{EMAIL}}', htmlspecialchars($user['email']), $html);
$html = str_replace('{{LOGOUT_LINK}}', '../../logout.php', $html);
$html = str_replace('{{PROFILE_LINK}}', 'profile.php', $html);
$html = str_replace('{{STATUS}}', htmlspecialchars($user['statut']), $html);

// Afficher la page finale
echo $html;
