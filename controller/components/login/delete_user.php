<?php
session_start();

// Vérification que l'utilisateur est admin
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header('Location: ../../../view/Client/login/login.html');
    exit;
}

require_once '../config.php';
$db = Config::getConnexion();

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // Récupérer l'utilisateur à supprimer
    $stmt = $db->prepare("SELECT role_id FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        die("Utilisateur introuvable !");
    }

    // Vérifier si c'est un admin
    if($user['role_id'] == 1){
        die("Impossible de supprimer un autre admin !");
    }

    // Supprimer l'utilisateur
    $stmt = $db->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirection vers le dashboard
header("Location: ../../../iew/Client/login/login.html");
exit;
?>
