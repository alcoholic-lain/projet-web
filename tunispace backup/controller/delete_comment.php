<?php
session_start();
if (!isset($_SESSION["admin"])) {
    die("Access denied");
}

require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../view/admin_dashboard.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // Get post_id before deleting comment (to update counter)
    $stmt = $pdo->prepare("SELECT post_id FROM comments WHERE id = ?");
    $stmt->execute([$id]);
    $post_id = $stmt->fetchColumn();

    // Delete the comment
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);

    // Decrease comment counter
    if ($post_id) {
        $pdo->prepare("UPDATE articles SET comments = comments - 1 WHERE id = ?")->execute([$post_id]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}

header("Location: ../view/admin_dashboard.php#comments");
exit;