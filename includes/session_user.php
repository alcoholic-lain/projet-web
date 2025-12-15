<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../view/LoginC/login.html');
    exit;
}

// Récupérer les infos utilisateur depuis la session
$user = $_SESSION['user'];
$avatar = !empty($user['avatar']) ? $user['avatar'] : '../../assets/img/default-avatar.png';
$statusColor = ($user['statut'] === 'actif') ? '#4CAF50' : '#888';
$statusText  = ($user['statut'] === 'actif') ? 'Actif' : 'Inactif';
?>
