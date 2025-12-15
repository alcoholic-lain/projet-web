<?php
include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../controller/components/Reclamtion/ReclamationController.php';


$id = $_GET['id'] ?? 0;

if($id) {
    $controller = new ReclamationController();
    if($controller->deleteReclamation($id)) {
        header("Location: liste.php?message=supprime");
        exit();
    } else {
        header("Location: liste.php?message=erreur");
        exit();
    }
} else {
    header("Location: liste.php");
    exit();
}
?>