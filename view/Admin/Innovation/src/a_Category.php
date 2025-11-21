<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";


$controller = new CategoryController();
$error = null;

// Gestion suppression
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        try {
            $controller->deleteCategory($id);
            header("Location: a_Category.php?msg=deleted");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Récupération liste
try {
    $categories = $controller->listCategories();
} catch (Exception $e) {
    $categories = [];
    $error = $e->getMessage();
}

$msg = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration – Gestion des Catégories</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="with-sidebar">

<div class="sidebar">
    <h2>🚀 Admin</h2>

    <a href="../../index.php">
        <span class="icon">🏠</span>
        <span class="text">Dashboard</span>
    </a>

    <a href="a_Category.php" style="color:#FFB347; font-weight:bold;">
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
    <h1>🗂️ Espace Administrateur - Gestion des Catégories</h1>
    <nav>
        <a href="../../index.php">Tableau de bord</a>
        <a href="a_Category.php" style="color:#FFB347; font-weight:bold;">Catégories</a>
        <a href="a_Innovation.php">Innovations</a>
    </nav>
</header>

<main>
    <div class="text-center">
        <h2>Tableau des catégories</h2>
        <a href="add_Category.php" class="btn-add">➕ Ajouter une catégorie</a>
    </div>

    <?php if ($msg === 'deleted'): ?>
        <p class="success">✅ Catégorie supprimée avec succès.</p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error">❌ Erreur : <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <section class="section-box">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['id']) ?></td>
                        <td><?= htmlspecialchars($cat['nom']) ?></td>
                        <td><?= htmlspecialchars($cat['description']) ?></td>
                        <td><?= htmlspecialchars($cat['date_creation']) ?></td>
                        <td>
                            <a href="edit_Category.php?id=<?= urlencode($cat['id']) ?>">✏️ Modifier</a>
                            <a href="a_Category.php?delete=<?= urlencode($cat['id']) ?>"
                               onclick="return confirm('Supprimer cette catégorie ?');">
                                🗑 Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucune catégorie trouvée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>
<script src="../assets/js/admin.js"></script>
</body>
</html>
