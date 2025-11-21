<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

$id = intval($_GET['id'] ?? 0);
$data = $innCtrl->getInnovation($id);
$user_id = (int)$data['user_id'];
$date_creation = $data['date_creation'];


if (!$data) die("⚠️ Innovation introuvable.");

$categories = $catCtrl->listCategories();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $statut = trim($_POST['statut']);

    if ($titre === "" || $description === "" || $category_id <= 0) {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {
        $innovation = new Innovation(
                $id,
                $titre,
                $description,
                $category_id,
                $user_id,
                $statut,
                $date_creation
        );



        if ($innCtrl->updateInnovation($innovation)) {
            header("Location: a_Innovation.php?msg=updated");
            exit;
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Innovation</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body class="with-sidebar">

<div class="sidebar">
    <h2>🚀 Admin</h2>

    <a href="../../index.php">
        <span class="icon">🏠</span><span class="text">Dashboard</span>
    </a>

    <a href="a_Category.php">
        <span class="icon">🗂️</span><span class="text">Catégories</span>
    </a>

    <a href="a_Innovation.php" style="color:#FFB347; font-weight:bold;">
        <span class="icon">🚀</span><span class="text">Innovations</span>
    </a>

    <a href="../../../Client/index.php">
        <span class="icon">🌐</span><span class="text">Front Office</span>
    </a>
</div>

<header>
    <h1>✏️ Modifier une Innovation</h1>
    <nav>
        <a href="a_Innovation.php">⬅ Retour</a>
    </nav>
</header>

<main class="section-box">

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">

        <label for="titre">Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($data['titre']) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($data['description']) ?></textarea>

        <label>Catégorie</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                        <?= ($cat['id'] == $data['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Statut</label>
        <select name="statut">
            <option value="En attente" <?= ($data['statut']=="En attente"?'selected':'') ?>>En attente</option>
            <option value="Validée" <?= ($data['statut']=="Validée"?'selected':'') ?>>Validée</option>
            <option value="Rejetée" <?= ($data['statut']=="Rejetée"?'selected':'') ?>>Rejetée</option>
        </select>

        <button class="btn-add">Mettre à jour</button>

    </form>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>
<script src="../assets/js/admin.js"></script>
</body>
</html>
