<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: admin_dashboard.php");
    exit;
}

// Get comment + post title
$stmt = $pdo->prepare("
    SELECT c.*, a.title 
    FROM comments c 
    LEFT JOIN articles a ON c.post_id = a.id 
    WHERE c.id = ? AND c.author = 'Admin'
");
$stmt->execute([$id]);
$comment = $stmt->fetch();

if (!$comment) {
    {
    die("Comment not found or you don't have permission to edit it.");
}}

// Handle update
if ($_POST['content'] ?? false) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
        $stmt->execute([$content, $id]);
        header("Location: admin_dashboard.php?updated=1#comments");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Comment - TUNISPACE</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { background: #0f0f1a; color: #e0f0ff; font-family: 'Segoe UI'; padding: 40px; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { background: #151525; padding: 40px; border-radius: 20px; border: 3px solid #00e1ff; width: 600px; box-shadow: 0 0 40px rgba(0,225,255,0.2); }
        h2 { color: #00e1ff; text-align: center; margin-bottom: 20px; }
        textarea { width: 100%; height: 180px; padding: 16px; background: #1a1a2e; border: 2px solid #00e1ff; border-radius: 12px; color: white; font-size: 1rem; }
        .info { background: rgba(0,225,255,0.1); padding: 15px; border-radius: 10px; margin: 20px 0; }
        .btn { padding: 14px 30px; border-radius: 12px; font-weight: bold; text-decoration: none; display: inline-block; margin: 10px; }
        .btn-primary { background: linear-gradient(135deg, #00e1ff, #0099cc); color: black; }
        .btn-secondary { background: #334455; color: white; }
    </style>
</head>
<body>

<div class="card">
    <h2>Edit Admin Comment</h2>

    <div class="info">
        <strong>Post:</strong> <?= htmlspecialchars($comment['title'] ?? '[Deleted Post]') ?><br>
        <strong>Current comment:</strong><br>
        <em><?= htmlspecialchars($comment['content']) ?></em>
    </div>

    <form method="POST">
        <textarea name="content" required placeholder="Edit your comment..."><?= htmlspecialchars($comment['content']) ?></textarea>
        <div style="text-align:center; margin-top:20px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="admin_dashboard.php#comments" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>