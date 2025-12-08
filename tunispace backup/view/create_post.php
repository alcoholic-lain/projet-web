<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Post - TUNISPACE</title>
  <link rel="stylesheet" href="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/assets/style.css">
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="side-item">🏠</div>
    <div class="side-item">🔍</div>
    <div class="side-item">⚙️</div>
    <div class="side-item">👤</div>
  </div>

  <!-- Topbar -->
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
    <div class="search-box">
      <input type="text" placeholder="Rechercher..." />
    </div>
  </header>

  <!-- Main content -->
  <section class="content" style="text-align: center; margin-top: 120px;">
    <h1>Create New Post</h1>
    <p>Fill out the fields to add a new article.</p>

    <div class="create-post-form" style="max-width: 600px; margin: 0 auto; padding: 20px; background: rgba(10,10,40,0.75); border-radius: 15px;">
      <form action="../../../../Users/nadhem/PhpstormProjects/projet-web/tunispace%20backup/controller/PostController.php" method="POST">
        <label>Title:</label>
        <input type="text" name="title" placeholder="Article title" required>

        <label>Keywords:</label>
        <input type="text" name="keywords" placeholder="e.g., space, stars" required>

        <label>Content:</label>
        <textarea name="content" rows="6" placeholder="Write the article content..." required></textarea>

        <label>Image filename (in assets folder):</label>
        <input type="text" name="image" placeholder="mars.jpg" required>

        <button type="submit" name="submit" style="margin-top: 15px; background: #00e1ff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer;">Create Post</button>
      </form>
    </div>
  </section>

</body>
</html>
