<?php
require_once '../controller/PostController.php';

if (!isset($_GET['id'])) {
    die("No post ID provided");
}

$id = intval($_GET['id']);
$post = getPostById($id);

if (!$post) {
    die("Post not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $keywords = $_POST['keywords'];
    $content = $_POST['content'];
    $image = $_POST['image'];

    updatePost($id, $title, $keywords, $content, $image);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Post</title>
<link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
  <div class="form-container">
    <h2>Edit Post</h2>
    <form method="POST">
      <label>Title:</label>
      <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required />

      <label>Keywords:</label>
      <input type="text" name="keywords" value="<?= htmlspecialchars($post['keywords']) ?>" />

      <label>Content:</label>
      <textarea name="content" rows="6"><?= htmlspecialchars($post['content']) ?></textarea>

      <label>Image:</label>
      <input type="text" name="image" value="<?= htmlspecialchars($post['image']) ?>" />

      <button type="submit">Update Post</button>
    </form>
  </div>
</body>
</html>
