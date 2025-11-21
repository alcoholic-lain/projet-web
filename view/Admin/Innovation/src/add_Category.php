<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$controller = new CategoryController();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);

    if ($nom === "") {
        $error = "⚠️ Le nom de la catégorie est obligatoire.";
    } else {
        $cat = new Category(null, $nom, $description, null);
        try {
            $controller->addCategory($cat);
            header("Location: a_Category.php?msg=added");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Catégorie</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body class="with-sidebar">

<div class="sidebar">
    <h2>🚀 Admin</h2>

    <a href="../../index.php">
        <span class="icon">🏠</span>
        <span class="text">Dashboard</span>
    </a>

    <a href="a_Category.php" style="color:#FFB347;">
        <span class="icon">🗂️</span>
        <span class="text">Catégories</span>
    </a>

    <a href="a_Innovation.php">
        <span class="icon">🚀</span>
        <span class="text">Innovations</span>
    </a>

    <a href="../../../Client/index.php">
        <span class="icon">🌐</span>
        <span class="text">Front Office</span>
    </a>
</div>

<header>
    <h1>➕ Ajouter une Catégorie</h1>
    <nav>
        <a href="a_Category.php">⬅ Retour</a>
    </nav>
</header>
<main class="section-box">

    <?php if ($error): ?>
        <p class="error">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">

        <label>Nom</label>
        <input type="text" name="nom" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <button class="btn-add">Créer la Catégorie</button>

    </form>

</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>
<script src="../assets/js/admin.js"></script>

</body>
</html>
