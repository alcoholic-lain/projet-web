<?php
require_once "../config.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = config::getConnexion();

    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        echo json_encode(['success' => false, 'message' => 'Email manquant.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode(['success' => false, 'message' => "Cet email n'existe pas."]);
        exit;
    }

    $token = bin2hex(random_bytes(32));

    $insertStmt = $conn->prepare("
        INSERT INTO password_resets (email, token, created_at) 
        VALUES (:email, :token, NOW())
        ON DUPLICATE KEY UPDATE token = :token, created_at = NOW()
    ");
    $insertStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $insertStmt->bindParam(':token', $token, PDO::PARAM_STR);
    $insertStmt->execute();

    echo json_encode([
        'success' => true,
        'token' => $token
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    exit;
}
?>
