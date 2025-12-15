<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
$db = Config::getConnexion();

// 🔹 Mettre l'utilisateur inactif avant de détruire la session
if(isset($_SESSION['user_id'])){
    $stmt = $db->prepare("UPDATE user SET statut = 'inactif' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

// 🔹 Détruire la session
session_unset();
session_destroy();

// 🔹 Supprimer tous les cookies Remember Me
$cookie_params = ['remember','user_id','user_pseudo','role_id'];
foreach($cookie_params as $c) {
    setcookie($c, '', time() - 3600, '/'); // Effacer cookie
}

// 🔹 Redirection vers la page de connexion
header("Location: view/LoginC/login.html");
exit;
