<?php
require_once '../controller/PostController.php';
$articles = getAllPosts();

// ————————————————————————
// AJAX HANDLER: Likes + REAL Comments (saved in DB)
// ————————————————————————
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['like_post']) || isset($_POST['add_comment']))) {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false]);
        exit;
    }

    // LIKE
    if (isset($_POST['like_post'])) {
        $pdo->prepare("UPDATE articles SET likes = likes + 1 WHERE id = ?")->execute([$id]);
        $likes = $pdo->query("SELECT likes FROM articles WHERE id = $id")->fetchColumn();
        echo json_encode(['success' => true, 'likes' => $likes]);
        exit;
    }

    // REAL COMMENT (saved in comments table)
    if (isset($_POST['add_comment'])) {
        $text = trim($_POST['text'] ?? '');
        if ($text !== '') {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, content, author) VALUES (?, ?, 'Anonymous')");
            $stmt->execute([$id, htmlspecialchars($text)]);

            // Update counter
            $pdo->prepare("UPDATE articles SET comments = comments + 1 WHERE id = ?")->execute([$id]);
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

// ————————————————————————
// FUNCTION: Load real comments from DB
// ————————————————————————
function getCommentsForPost($postId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT author, content, created_at FROM comments WHERE post_id = ? ORDER BY id DESC LIMIT 50");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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

    <div class="top-buttons">
      <a href="create_post.php" class="top-btn">+ Create Post</a>
      <a href="login.php" class="top-btn">Admin Login</a>
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
      $keywords = $article['keywords'] ?? "";
    ?>
      <div class="article" data-id="<?= $article['id'] ?>" data-keywords="<?= htmlspecialchars($keywords) ?>">
        <h2 class="article-title"><?= $article['title'] ?></h2>
        <img src="<?= $imagePath ?>" alt="<?= $article['title'] ?>" class="article-image" />
        <p class="article-text"><?= $article['content'] ?></p>

        <div class="actions">
          <button class="like-btn">Like (<span class="likeCount"><?= $article['likes'] ?></span>)</button>
          <span style="margin-left:20px; color:#00e1ff;">Comments: <?= $article['comments'] ?></span>
        </div>

        <div class="article-comments">
          <h3>Comments</h3>

          <!-- REAL COMMENTS LOADED FROM DATABASE -->
          <div class="comments-list">
            <?php 
            $realComments = getCommentsForPost($article['id']);
            foreach($realComments as $c): ?>
              <div style="padding:12px; margin:10px 0; background:rgba(0,225,255,0.08); border-radius:12px; border-left:3px solid #00e1ff;">
                <strong style="color:#00e1ff;"><?= htmlspecialchars($c['author']) ?>:</strong>
                <?= htmlspecialchars($c['content']) ?>
                <div style="font-size:0.8rem; color:#00e1ff99; margin-top:4px;">
                  <?= date('M j, Y \a\t H:i', strtotime($c['created_at'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="add-comment">
            <input type="text" class="commentInput" placeholder="Write a comment..." />
            <button class="commentBtn">Post</button>
          </div>
        </div>

        <div class="admin-actions" style="display:none;">
            <a href="edit_post.php?id=<?= $article['id'] ?>" class="edit-btn">Edit</a>
            <a href="#" class="delete-btn" data-id="<?= $article['id'] ?>">Delete</a>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <footer>
    <p>Made with love by <strong>TUNISPACE</strong> | 2025</p>
  </footer>

  <!-- JAVASCRIPT: Likes + Real Comments + Live Search -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const articles = document.querySelectorAll(".article");

      articles.forEach(article => {
        const id = article.dataset.id;
        const likeBtn = article.querySelector(".like-btn");
        const likeCount = article.querySelector(".likeCount");
        const commentInput = article.querySelector(".commentInput");
        const commentBtn = article.querySelector(".commentBtn");
        const commentsList = article.querySelector(".comments-list");

        // LIKE
        likeBtn.addEventListener("click", () => {
          fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `like_post=1&id=${id}`
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              likeCount.textContent = data.likes;
              likeBtn.style.background = "#00e1ff";
              likeBtn.style.color = "#000";
            }
          });
        });

        // ADD COMMENT
        const sendComment = () => {
          const text = commentInput.value.trim();
          if (!text) return;

          fetch("", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `add_comment=1&id=${id}&text=${encodeURIComponent(text)}`
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              const div = document.createElement("div");
              div.style.cssText = "padding:12px; margin:10px 0; background:rgba(0,225,255,0.08); border-radius:12px; border-left:3px solid #00e1ff;";
              div.innerHTML = `
                <strong style="color:#00e1ff;">Anonymous:</strong> ${text}
                <div style="font-size:0.8rem; color:#00e1ff99; margin-top:4px;">Just now</div>
              `;
              commentsList.prepend(div);
              commentInput.value = "";
            }
          });
        };

        commentBtn.onclick = sendComment;
        commentInput.addEventListener("keypress", e => e.key === "Enter" && sendComment());
      });

      // LIVE SEARCH
      const searchInput = document.querySelector(".search-box input");
      searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll(".article").forEach(article => {
          const text = article.textContent.toLowerCase() + (article.dataset.keywords || "").toLowerCase();
          article.style.display = text.includes(query) ? "block" : "none";
        });
      });
    });
  </script>

</body>
</html>