<?php
require_once "../config.php";

$conn = config::getConnexion();
header('Content-Type: application/json; charset=utf-8');

$token = trim($_POST['token'] ?? '');
$newPassword = trim($_POST['newPassword'] ?? '');

if (!$token || !$newPassword) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis.']);
    exit;
}

// Vérifier le token et récupérer l'email
$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = :token AND created_at >= NOW() - INTERVAL 1 HOUR");
$stmt->bindParam(':token', $token, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré.']);
    exit;
}

$email = $row['email'];

// Vérifier que le nouveau mot de passe n'est pas le même que l'ancien
$checkStmt = $conn->prepare("SELECT password FROM user WHERE email = :email");
$checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
$checkStmt->execute();
$user = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($newPassword, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas réutiliser votre ancien mot de passe.']);
    exit;
}

// Hasher le nouveau mot de passe
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe
$updateStmt = $conn->prepare("UPDATE user SET password = :password WHERE email = :email");
$updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
$updateStmt->execute();

// Supprimer le token
$deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
$deleteStmt->bindParam(':token', $token, PDO::PARAM_STR);
$deleteStmt->execute();

echo json_encode(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.']);
?>
