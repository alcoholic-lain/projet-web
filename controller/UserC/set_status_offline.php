<?php
session_start();
require_once '../../config.php';
$db = Config::getConnexion();

if(isset($_SESSION['user_id']) && (!isset($_SESSION['remember']) || !$_SESSION['remember'])){
    $stmt = $db->prepare("UPDATE user SET statut = 'inactif' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
?>