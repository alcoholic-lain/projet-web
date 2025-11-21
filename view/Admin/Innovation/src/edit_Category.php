<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$controller = new CategoryController();

$id = intval($_GET["id"] ?? 0);
$data = $controller->getCategory($id);

if (!$data) die("⚠️ Catégorie introuvable.");

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);

    if ($nom === "") {
        $error = "⚠️ Le nom est obligatoire.";
    } else {
        $cat = new Category(
                $id,
                $nom,
                $description,
                $data["date_creation"]
        );

        try {
            $controller->updateCategory($cat);
            header("Location: a_Category.php?msg=updated");
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
    <title>Modifier Catégorie</title>
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
    <h1>✏️ Modifier la Catégorie</h1>
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
        <input type="text" name="nom" value="<?= htmlspecialchars($data['nom']) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($data['description']) ?></textarea>

        <button class="btn-add">Mettre à jour</button>

    </form>

</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>
<script src="../assets/js/admin.js"></script>
</body>
</html>
