<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
$db = Config::getConnexion();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Restaurer session depuis cookies si session vide
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['pseudo'] = $_COOKIE['user_pseudo'] ?? '';
    $_SESSION['role_id'] = $_COOKIE['role_id'] ?? '';
    $_SESSION['remember'] = true;

    // Statut actif
    $stmt = $db->prepare("UPDATE user SET statut = 'actif' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

// Redirection si pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../LoginC/login.html");
    exit;
}
?>