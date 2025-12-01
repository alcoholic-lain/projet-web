<?php
session_start();
if (!isset($_SESSION["admin"])) exit;

require_once __DIR__ . '/../db.php';

$post_id = (int)$_POST['post_id'];
$content = trim($_POST['content']);

if ($post_id > 0 && $content !== '') {
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, author, content) VALUES (?, 'Admin', ?)");
    $stmt->execute([$post_id, $content]);

    $pdo->prepare("UPDATE articles SET comments = comments + 1 WHERE id = ?")->execute([$post_id]);
}

header("Location: ../view/admin_dashboard.php");
exit;