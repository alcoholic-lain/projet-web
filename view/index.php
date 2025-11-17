<?php
require_once '../controller/PostController.php';
$articles = getAllPosts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TUNISPACE</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <script defer src="../assets/script.js"></script>
</head>
<body>

  <!-- LEFT SIDE BAR -->
  <div class="sidebar">
    <div class="side-item">🏠</div>
    <div class="side-item">🔍</div>
    <div class="side-item">⚙️</div>
    <div class="side-item">👤</div>
  </div>

  <!-- TOP NAV BAR -->
  <header class="topbar">
    <nav>
      <a class="active">Accueil</a>
      <a>Travaux</a>
      <a>Services</a>
      <a>Blog</a>
      <a>À propos</a>
      <a>Boutique</a>
      <a>Contact</a>
    </nav>

    <!-- Top-left buttons -->
    <div class="top-buttons">
      <a href="create_post.php" class="top-btn">➕ Create Post</a>
      <a href="#" id="adminLoginBtn" class="top-btn">🔒 Admin Login</a>
    </div>

    <div class="search-box">
      <input type="text" placeholder="Rechercher..." />
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <section class="content">
    <h1>TUNISPACE</h1>
    <p>Votre espace pour explorer l'univers technologique.</p>
  </section>

  <!-- ARTICLES -->
  <section class="articles">
    <?php foreach($articles as $article):
      $imagePath = $article['image'];
      if (!str_contains($imagePath, 'assets/')) {
          $imagePath = '../assets/' . $imagePath;
      }
    ?>
      <div class="article" data-id="<?= $article['id'] ?>" data-keywords="<?= $article['keywords'] ?>">
        <h2 class="article-title"><?= $article['title'] ?></h2>
        <img src="<?= $imagePath ?>" alt="<?= $article['title'] ?>" class="article-image" />
        <p class="article-text"><?= $article['content'] ?></p>
        <div class="actions">
          <button class="like-btn">❤️ Like (<span class="likeCount"><?= $article['likes'] ?></span>)</button>
        </div>

        <div class="article-comments">
          <h3>💬 Comments</h3>
          <div class="comments-list"></div>
          <div class="add-comment">
            <input type="text" class="commentInput" placeholder="Write a comment..." />
            <button class="commentBtn">Post</button>
          </div>
        </div>

        <!-- Admin actions (hidden by default) -->
        <div class="admin-actions" style="display:none;">
            <a href="edit_post.php?id=<?= $article['id'] ?>" class="edit-btn">✏️ Edit</a>
            <a href="#" class="delete-btn" data-id="<?= $article['id'] ?>">🗑️ Delete</a>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <footer>
    <p>⭐ Made with love by <strong>TUNISPACE</strong> | 2025</p>
  </footer>

</body>
</html>
