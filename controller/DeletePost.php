<?php
header('Content-Type: application/json');
require_once 'PostController.php';

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No ID']);
    exit;
}

$id = intval($_POST['id']);

$stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
$success = $stmt->execute([$id]);

echo json_encode([
    'status' => $success ? 'success' : 'error'
]);
