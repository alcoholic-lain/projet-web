<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once "../model/Article.php";

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: login.php");
    exit;
}

$articleModel = new Article($pdo);
$articles = $articleModel->getAll();
$totalLikes = array_sum(array_column($articles, 'likes'));

// Get all comments with post title
$stmt = $pdo->query("
    SELECT c.*, a.title 
    FROM comments c 
    LEFT JOIN articles a ON c.post_id = a.id 
    ORDER BY c.created_at DESC
");
$allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TUNISPACE</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { background: #0f0f1a; color: #e0f0ff; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; min-height: 100vh; }
        h1, h2 { color: #00e1ff; text-shadow: 0 0 15px #00e1ff44; }
        .container { max-width: 1500px; margin: 0 auto; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 2px solid #00e1ff33; margin-bottom: 30px; }
        .btn { padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: bold; transition: all 0.3s; margin: 5px; display: inline-block; }
        .btn-primary { background: linear-gradient(135deg, #00e1ff, #0099cc); color: #000; }
        .btn-danger { background: #ff3366; color: white; }
        .btn-small { padding: 6px 12px; font-size: 0.9rem; }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 25px #00e1ff66; }
        .btn-danger:hover { background: #ff0044; }
        table { width: 100%; border-collapse: collapse; margin: 25px 0; background: rgba(15,15,35,0.9); border-radius: 15px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,225,255,0.15); }
        th { background: #00e1ff22; padding: 18px; text-align: left; color: #00e1ff; font-weight: bold; }
        td { padding: 16px; border-bottom: 1px solid #00e1ff22; }
        tr:hover { background: rgba(0,225,255,0.12); }
        .admin-badge { background: #ff00aa; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .tab-buttons { display: flex; gap: 15px; margin: 30px 0; }
        .tab-btn { padding: 14px 32px; background: #1a1a2e; border: 2px solid #334455; color: #e0f0ff; cursor: pointer; border-radius: 12px; transition: all 0.3s; }
        .tab-btn.active { background: #00e1ff22; border-color: #00e1ff; color: #00e1ff; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .add-comment-form { margin: 15px 0; padding: 15px; background: rgba(0,225,255,0.08); border-radius: 12px; border-left: 4px solid #00e1ff; }
        .add-comment-form textarea { width: 100%; padding: 12px; background: #1a1a2e; border: 2px solid #334455; border-radius: 10px; color: white; resize: vertical; }
        .add-comment-form button { margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>ADMIN DASHBOARD</h1>
        <a href="../controller/logout.php" class="btn-danger">Logout</a>
    </div>

    <div style="background: rgba(0,225,255,0.1); padding: 20px; border-radius: 15px; border-left: 5px solid #00e1ff; margin-bottom: 30px;">
        <strong>Total Likes: <span style="font-size:2rem; color:#00e1ff;"><?= $totalLikes ?></span></strong>
    </div>

    <a href="create_post.php" class="btn btn-primary">+ Create New Post</a>

    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab('posts')">Posts</button>
        <button class="tab-btn" onclick="openTab('comments')">Comments (<?= count($allComments) ?>)</button>
    </div>

    <!-- POSTS TAB -->
    <div id="posts" class="tab-content active">
        <h2>Manage Posts</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Image</th>
                <th>Likes</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td><strong><?= $a['id'] ?></strong></td>
                <td><?= htmlspecialchars($a['title']) ?></td>
                <td><img src="../assets/<?= $a['image'] ?>" width="100" style="border-radius:10px;"></td>
                <td><strong style="color:#00e1ff"><?= $a['likes'] ?></strong></td>
                <td><strong style="color:#00ffaa"><?= $a['comments'] ?></strong></td>
                <td>
                    <a class="btn btn-primary btn-small" href="edit_post.php?id=<?= $a['id'] ?>">Edit</a>
                    <a class="btn btn-danger btn-small" href="../controller/deletepost.php?id=<?= $a['id'] ?>"
                       onclick="return confirm('Delete post AND all comments?')">Delete</a>
                    <a class="btn btn-primary btn-small" href="#" onclick="showAddComment(<?= $a['id'] ?>, '<?= addslashes(htmlspecialchars($a['title'])) ?>')">Add Comment</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- COMMENTS TAB -->
    <div id="comments" class="tab-content">
        <h2>Manage Comments</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Post</th>
                <th>Author</th>
                <th>Content</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($allComments as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td>
                    <?php if ($c['title']): ?>
                        <em><?= htmlspecialchars($c['title']) ?></em>
                    <?php else: ?>
                        <span style="color:#ff6666;">[Deleted Post]</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?= htmlspecialchars($c['author']) ?>
                    <?php if ($c['author'] === 'Admin'): ?>
                        <span class="admin-badge">ADMIN</span>
                    <?php endif; ?>
                </td>
                <td style="max-width:500px; word-wrap:break-word;"><?= htmlspecialchars($c['content']) ?></td>
                <td><?= date('M j, Y H:i', strtotime($c['created_at'])) ?></td>
                <td>
                    <?php if ($c['author'] === 'Admin'): ?>
                        <a class="btn btn-primary btn-small" href="edit_comment.php?id=<?= $c['id'] ?>">Edit</a>
                    <?php endif; ?>
                    <a class="btn btn-danger btn-small" href="../controller/delete_comment.php?id=<?= $c['id'] ?>"
                       onclick="return confirm('Delete forever?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- Add Comment Modal -->
<div id="addCommentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:999; justify-content:center; align-items:center;">
    <div style="background:#151525; padding:30px; border-radius:20px; border:3px solid #00e1ff; width:500px; max-width:90%;">
        <h2 style="margin-top:0; text-align:center;">Add Admin Comment</h2>
        <p><strong>Post:</strong> <span id="modalPostTitle"></span></p>
        <form action="../controller/add_admin_comment.php" method="POST">
            <input type="hidden" name="post_id" id="modalPostId">
            <textarea name="content" required placeholder="Write your admin comment..." style="width:100%; height:120px; padding:15px; background:#1a1a2e; border:2px solid #00e1ff; border-radius:12px; color:white;"></textarea>
            <div style="text-align:center; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Post as Admin</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('addCommentModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add('active');
}

function showAddComment(postId, postTitle) {
    document.getElementById('modalPostId').value = postId;
    document.getElementById('modalPostTitle').textContent = postTitle;
    document.getElementById('addCommentModal').style.display = 'flex';
}
</script>

</body>
</html>