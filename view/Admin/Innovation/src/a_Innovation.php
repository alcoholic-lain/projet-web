<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$ctrl = new InnovationController();
$error = null;

// Gestion suppression
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        try {
            $ctrl->deleteInnovation($id);
            header("Location: a_Innovation.php?msg=deleted");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

try {
    $innovations = $ctrl->listInnovations();
} catch (Exception $e) {
    $innovations = [];
    $error = $e->getMessage();
}

$msg = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration – Gestion des Innovations</title>
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body class="with-sidebar">

<div class="sidebar">
    <h2>🚀 Admin</h2>

    <a href="../../index.php">
        <span class="icon">🏠</span>
        <span class="text">Dashboard</span>
    </a>

    <a href="a_Category.php">
        <span class="icon">🗂️</span>
        <span class="text">Catégories</span>
    </a>

    <a href="a_Innovation.php" style="color:#FFB347; font-weight:bold;">
        <span class="icon">🚀</span>
        <span class="text">Innovations</span>
    </a>

    <a href="../../../Client/index.php">
        <span class="icon">🌐</span>
        <span class="text">Front Office</span>
    </a>
</div>

<header>
    <h1>🚀 Espace Administrateur - Gestion des Innovations</h1>
    <nav>
        <a href="../../index.php">Tableau de bord</a>
        <a href="a_Category.php">Catégories</a>
        <a href="a_Innovation.php" style="color:#FFB347; font-weight:bold;">Innovations</a>
        <a href="../../../Client/index.php">Front Office</a>
    </nav>
</header>

<main>
    <div class="text-center">
        <h2>Tableau des innovations</h2>
        <a href="add_Innovation.php" class="btn-add">➕ Ajouter une innovation</a>
    </div>

    <?php if ($msg === 'deleted'): ?>
        <p class="success">✅ Innovation supprimée avec succès.</p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error">❌ Erreur : <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <section class="section-box">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php if (!empty($innovations)): ?>
                <?php foreach ($innovations as $inn): ?>
                    <tr>
                        <td><?= htmlspecialchars($inn['id']) ?></td>
                        <td><?= htmlspecialchars($inn['titre']) ?></td>
                        <td><?= htmlspecialchars($inn['description']) ?></td>
                        <td><?= htmlspecialchars($inn['categorie_nom']) ?></td>
                        <td><?= htmlspecialchars($inn['statut']) ?></td>
                        <td><?= htmlspecialchars($inn['date_creation']) ?></td>
                        <td>
                            <a href="edit_Innovation.php?id=<?= urlencode($inn['id']) ?>">✏️ Modifier</a>
                            <a href="a_Innovation.php?delete=<?= urlencode($inn['id']) ?>"
                               onclick="return confirm('Supprimer cette innovation ?');">
                                🗑 Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">Aucune innovation trouvée.</td></tr>
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
