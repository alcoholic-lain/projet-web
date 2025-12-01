<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../db.php';
require_once '../model/Article.php';

if (!isset($_GET['id'])) {
    header("Location: ../view/admin_dashboard.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $pdo->beginTransaction();

    // DELETE ALL COMMENTS FIRST
    $stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt->execute([$id]);

    // DELETE THE ARTICLE
    $articleModel = new Article($pdo);
    $articleModel->delete($id);

    $pdo->commit();

    header("Location: ../view/admin_dashboard.php?deleted=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../view/admin_dashboard.php?error=1");
    exit;
}
?>