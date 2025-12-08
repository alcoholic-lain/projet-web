<?php
use model\Article;

session_start();
require_once __DIR__ . '/../db.php';  // Database connection

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: login.php");
    exit;
}

// DELETE REPORT – NOW 100% WORKING (fixed redirect)
if (isset($_GET['delete_report'])) {
    $rid = (int)$_GET['delete_report'];
    
    $stmt = $pdo->prepare("SELECT article_id FROM reports WHERE id = ?");
    $stmt->execute([$rid]);
    $aid = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM reports WHERE id = ?")->execute([$rid]);

    if ($aid) {
        $pdo->prepare("UPDATE articles SET reports_count = GREATEST(reports_count - 1, 0) WHERE id = ?")
            ->execute([$aid]);
    }
    
    // FIXED: Correct filename
    header("Location: admin_dashboard.php#reports");
    exit;
}

require_once "../model/Article.php";

$articleModel = new Article($pdo);
$articles = $articleModel->getAll();
$totalLikes = array_sum(array_column($articles, 'likes'));

$stmt = $pdo->query("SELECT c.*, a.title FROM comments c LEFT JOIN articles a ON c.post_id = a.id ORDER BY c.created_at DESC");
$allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reportsStmt = $pdo->query("SELECT r.*, a.title FROM reports r LEFT JOIN articles a ON r.article_id = a.id ORDER BY r.reported_at DESC");
$allReports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TUNISPACE</title>
    <link rel="stylesheet" href="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/assets/style.css">
    <style>
        body{background:#0f0f1a;color:#e0f0ff;font-family:Segoe UI;margin:0;padding:20px;}
        h1,h2{color:#00e1ff;}
        .container{max-width:1500px;margin:0 auto;}
        .top-bar{display:flex;justify-content:space-between;align-items:center;padding:20px 0;border-bottom:2px solid #00e1ff33;margin-bottom:30px;}
        .btn{padding:12px 24px;border-radius:12px;text-decoration:none;font-weight:bold;margin:5px;display:inline-block;}
        .btn-primary{background:linear-gradient(135deg,#00e1ff,#0099cc);color:#000;}
        .btn-danger{background:#ff3366;color:white;}
        .btn-small{padding:6px 12px;font-size:0.9rem;}
        table{width:100%;border-collapse:collapse;margin:25px 0;background:rgba(15,15,35,0.9);border-radius:15px;overflow:hidden;box-shadow:0 10px 40px rgba(0,225,255,0.15);}
        th{background:#00e1ff22;padding:18px;color:#00e1ff;font-weight:bold;}
        td{padding:16px;border-bottom:1px solid #00e1ff22;vertical-align:top;}
        tr:hover{background:rgba(255,51,102,0.1);}
        .tab-buttons{display:flex;gap:15px;margin:30px 0;}
        .tab-btn{padding:14px 32px;background:#1a1a2e;border:2px solid #334455;color:#e0f0ff;cursor:pointer;border-radius:12px;}
        .tab-btn.active{background:#00e1ff22;border-color:#ff3366;color:#ff3366;}
        .tab-content{display:none;}
        .tab-content.active{display:block;}
        .report-box{
            background:rgba(255,51,102,0.15);
            padding:14px;
            border-radius:12px;
            border-left:4px solid #ff3366;
            font-size:0.95rem;
        }
        .report-reason{color:#ff6699;font-weight:bold;}
        .custom-msg{color:#ff99bb;margin-top:8px;}
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h1>ADMIN DASHBOARD</h1>
        <a href="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/controller/logout.php" class="btn-danger">Logout</a>
    </div>

    <div style="background:rgba(0,225,255,0.1);padding:20px;border-radius:15px;margin-bottom:30px;">
        <strong>Total Likes: <span style="font-size:2rem;color:#00e1ff;"><?= $totalLikes ?></span></strong>
        | <strong>Total Reports: <span style="font-size:2rem;color:#ff3366;"><?= count($allReports) ?></span></strong>
    </div>

    <a href="create_post.php" class="btn btn-primary">+ Create New Post</a>

    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab('posts')">Posts</button>
        <button class="tab-btn" onclick="openTab('comments')">Comments (<?= count($allComments) ?>)</button>
        <button class="tab-btn" onclick="openTab('reports')">Reports (<?= count($allReports) ?>)</button>
    </div>

    <!-- POSTS TAB -->
    <div id="posts" class="tab-content active">
        <h2>Manage Posts</h2>
        <table>
            <tr><th>ID</th><th>Title</th><th>Image</th><th>Likes</th><th>Comments</th><th>Reports</th><th>Actions</th></tr>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['title']) ?></td>
                <td><img src="../assets/<?= $a['image'] ?>" width="100" style="border-radius:10px;"></td>
                <td style="color:#00e1ff"><strong><?= $a['likes'] ?></strong></td>
                <td style="color:#00ffaa"><strong><?= $a['comments'] ?></strong></td>
                <td style="color:#ff3366"><strong><?= $a['reports_count'] ?></strong></td>
                <td>
                    <a class="btn btn-primary btn-small" href="edit_post.php?id=<?= $a['id'] ?>">Edit</a>
                    <a class="btn btn-danger btn-small" href="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/controller/deletepost.php?id=<?= $a['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- COMMENTS TAB -->
    <div id="comments" class="tab-content">
        <h2>Manage Comments</h2>
        <table>
            <tr><th>ID</th><th>Post</th><th>Author</th><th>Content</th><th>Date</th><th>Actions</th></tr>
            <?php foreach ($allComments as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= $c['title'] ? htmlspecialchars($c['title']) : '<span style="color:#ff6666;">[Deleted]</span>' ?></td>
                <td><?= htmlspecialchars($c['author']) ?></td>
                <td style="max-width:500px;word-wrap:break-word;"><?= htmlspecialchars($c['content']) ?></td>
                <td><?= date('M j, Y H:i', strtotime($c['created_at'])) ?></td>
                <td>
                    <a class="btn btn-danger btn-small" href="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/controller/delete_comment.php?id=<?= $c['id'] ?>" onclick="return confirm('Delete forever?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- REPORTS TAB -->
    <div id="reports" class="tab-content">
        <h2 style="color:#ff3366;">Reported Posts</h2>
        <?php if (empty($allReports)): ?>
            <p style="text-align:center;color:#00e1ff;font-size:1.5rem;">No reports yet – great community!</p>
        <?php else: ?>
        <table>
            <tr><th>ID</th><th>Article</th><th style="width:40%;">Report Details</th><th>Date</th><th>Action</th></tr>
            <?php foreach ($allReports as $r): ?>
            <tr>
                <td><strong><?= $r['id'] ?></strong></td>
                <td><?= $r['title'] ? "<strong>".htmlspecialchars($r['title'])."</strong>" : "<em style='color:#ff6666;'>[Deleted Post]</em>" ?></td>
                <td>
                    <div class="report-box">
                        <?php if (!empty(trim($r['reason']))): ?>
                            <div class="report-reason">Reasons: <?= nl2br(htmlspecialchars($r['reason'])) ?></div>
                        <?php endif; ?>
                        <?php if (!empty(trim($r['custom_reason']))): ?>
                            <div class="custom-msg"><strong>Custom message:</strong><br><?= nl2br(htmlspecialchars($r['custom_reason'])) ?></div>
                        <?php endif; ?>
                        <?php if (empty(trim($r['reason'])) && empty(trim($r['custom_reason']))): ?>
                            <em style="color:#666;">No details provided</em>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?= date('M j, Y H:i', strtotime($r['reported_at'])) ?></td>
                <td>
                    <!-- 100% WORKING DELETE LINK -->
                    <a class="btn btn-danger btn-small" 
                       href="<?= basename($_SERVER['PHP_SELF']) ?>?delete_report=<?= $r['id'] ?>" 
                       onclick="return confirm('Delete this report permanently?')">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function openTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add('active');
    window.location.hash = tabName;
}
if (window.location.hash) openTab(window.location.hash.substring(1));
</script>
</body>
</html>