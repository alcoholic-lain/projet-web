<?php
session_start();
require_once '../config.php';
require_once '../model/Role.php';

// Vérification admin
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header('Location: ../../FrontOffice/login.html');
    exit;
}

// --- Traitement AJAX ---
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');

    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if($nom === '') {
        echo json_encode(['success' => false, 'message' => 'Le nom du rôle est obligatoire.']);
        exit;
    }

    try {
        $db = Config::getConnexion();

        // Vérifier si le rôle existe déjà
        $checkStmt = $db->prepare("SELECT id FROM roles WHERE nom = :nom");
        $checkStmt->execute([':nom' => $nom]);
        if($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => "Le rôle '$nom' existe déjà."]);
            exit;
        }

        // Insérer le rôle
        $stmt = $db->prepare("INSERT INTO roles (nom, description) VALUES (:nom, :description)");
        $stmt->execute([':nom' => $nom, ':description' => $description]);

        echo json_encode(['success' => true, 'message' => 'Rôle créé avec succès !']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
    exit; // ⚠️ Important : pas de HTML après
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un rôle</title>
    <link rel="stylesheet" href="../view/FrontOffice/assets/css/edit_user.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Créer un rôle</h1>

    <p id="messageBox"></p>

    <form id="createRoleForm">
        <label>Nom du rôle :</label>
        <input type="text" name="nom">

        <label>Description :</label>
        <textarea name="description"></textarea>

        <button type="submit" class="btn-register">💾 Créer</button>
        <a href="../view/BackEnd/dashboard.php" class="btn-logout">Annuler</a>
    </form>
</div>
<script src="../view/FrontOffice/assets/js/create_role.js"></script>
</body>
</html>
