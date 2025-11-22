<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$controller = new CategoryController();
$error = null;
$msg = $_GET['msg'] ?? null;

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

try {
    $categories = $controller->listCategories();
} catch (Exception $e) {
    $categories = [];
    $error = $e->getMessage();
}

// Variables pour header + sidebar
$pageTitle     = "🗂️ Gestion des Catégories";
$pageSubtitle  = "Administration des catégories d'innovation";
$activeMenu    = 'categories';
$activeSub     = 'categories_list';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>

    <!-- CSS GLOBAL ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE À CETTE PAGE -->
    <link rel="stylesheet" href="../assets/css/a_Category.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <h2 class="section-title-main">Liste des catégories</h2>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div>
                <?php if ($msg === 'deleted'): ?>
                    <p class="success" style="margin:0;">✅ Catégorie supprimée avec succès.</p>
                <?php endif; ?>
                <?php if ($error): ?>
                    <p class="error" style="margin:0;">❌ <?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
            </div>
            <a href="add_Category.php" class="btn-add">➕ Ajouter une catégorie</a>
        </div>

        <section class="section-box">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date création</th>
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
                                <a href="edit_Category.php?id=<?= $cat['id'] ?>"
                                   class="btn-icon edit"
                                   title="Modifier">
                                    ✏️
                                </a>

                                <a href="a_Category.php?delete=<?= $cat['id'] ?>"
                                   class="btn-icon delete"
                                   onclick="return confirm('Supprimer cette catégorie ?');"
                                   title="Supprimer">
                                    🗑️
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

    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE À CETTE PAGE -->
<script src="../assets/js/a_Category.js"></script>

</body>
</html>
