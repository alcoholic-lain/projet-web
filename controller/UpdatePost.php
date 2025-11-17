<?php
header('Content-Type: application/json');
require_once 'PostController.php';

if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'No action']);
    exit;
}

$action = $_POST['action'];
$id = intval($_POST['id']);

if ($action === 'like') {
    $liked = $_POST['liked'] === 'true' ? 1 : 0;

    $stmt = $pdo->prepare("SELECT likes FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $current = intval($stmt->fetchColumn() ?? 0);

    $newLikes = $liked ? $current + 1 : max($current - 1, 0);

    $stmt = $pdo->prepare("UPDATE articles SET likes = ? WHERE id = ?");
    $stmt->execute([$newLikes, $id]);

    echo json_encode(['status' => 'success', 'likes' => $newLikes]);
    exit;
}

if ($action === 'comment') {
    $comment = trim($_POST['comment']);

    $stmt = $pdo->prepare("SELECT comments FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $current = intval($stmt->fetchColumn() ?? 0);

    $newComments = $current + 1;

    $stmt = $pdo->prepare("UPDATE articles SET comments = ? WHERE id = ?");
    $stmt->execute([$newComments, $id]);

    echo json_encode(['status' => 'success', 'comments' => $newComments]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
