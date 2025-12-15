<?php
session_start();
require_once '../../config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || !isset($_POST['status'])){
    echo json_encode(['success'=>false,'message'=>'Données manquantes']);
    exit;
}

try{
    $status = ($_POST['status']==='inactif') ? 'inactif' : 'actif';
    $db = config::getConnexion();
    $stmt = $db->prepare("UPDATE user SET statut=? WHERE id=?");
    $stmt->execute([$status,$_SESSION['user_id']]);
    echo json_encode(['success'=>true,'status'=>$status]);
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>