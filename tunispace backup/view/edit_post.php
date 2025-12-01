<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../db.php';
require_once "../model/Article.php";

$articleModel = new Article($pdo);
$article = $articleModel->getById($_GET['id']);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - TUNISPACE</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            background: #0f0f1a;
            color: #e0e0ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .edit-container {
            max-width: 800px;
            margin: 40px auto;
            background: #151525;
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 231, 255, 0.1);
            border: 1px solid #00e1ff33;
        }

        h2 {
            text-align: center;
            font-size: 2.4rem;
            margin-bottom: 30px;
            color: #00e1ff;
            text-shadow: 0 0 15px #00e1ff44;
            letter-spacing: 1px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        input[type="text"],
        input[type="file"],
        textarea {
            padding: 16px 20px;
            border: 2px solid #334455;
            border-radius: 12px;
            background: #1a1a2e;
            color: #e0f0ff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #00e1ff;
            box-shadow: 0 0 15px #00e1ff33;
            background: #1e1e38;
        }

        textarea {
            min-height: 180px;
            resize: vertical;
            font-family: inherit;
        }

        .current-image {
            text-align: center;
            margin: 15px 0;
        }

        .current-image img {
            border-radius: 12px;
            border: 3px solid #00e1ff44;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
        }

        button {
            padding: 16px 30px;
            background: linear-gradient(135deg, #00e1ff, #00aaff);
            color: #000;
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            align-self: center;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px #00e1ff66;
            background: linear-gradient(135deg, #00f0ff, #00bbff);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #00e1ff;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        label {
            color: #00e1ff;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>✏️ Edit Post</h2>

    <form action="../controller/updatepost.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $article['id'] ?>">

        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($article['title']) ?>" required>
        </div>

        <div>
            <label for="keywords">Keywords (comma separated)</label>
            <input type="text" name="keywords" id="keywords" value="<?= htmlspecialchars($article['keywords']) ?>" required>
        </div>

        <div>
            <label for="content">Content</label>
            <textarea name="content" id="content" required><?= htmlspecialchars($article['content']) ?></textarea>
        </div>

        <div class="current-image">
            <p><strong>Current Image:</strong></p>
            <img src="../assets/<?= htmlspecialchars($article['image']) ?>" alt="Current" width="300">
        </div>

        <div>
            <label for="image">Change Image (optional)</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <button type="submit">Update Post</button>
    </form>

    <a href="../view/admin_dashboard.php" class="back-link">Back to Dashboard</a>
</div>
</body>
</html>