<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$catCtrl = new CategoryController();
$error = null;

// Activation sidebar
$activeMenu = 'categories';
$activeSub  = 'categories_add';


// Soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);

    if ($nom === "" || $description === "") {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {

        $category = new Category(
                null,
                $nom,
                $description,
                null
        );

        if ($catCtrl->addCategory($category)) {
            header("Location: a_Category.php?msg=added");
            exit;
        } else {
            $error = "Erreur lors de l’ajout.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Catégorie</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE -->
    <link rel="stylesheet" href="../assets/css/add_Category.css">
</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <div class="page-header-row">
            <h2 class="section-title-main">🗂️ Ajouter une Catégorie</h2>
            <a href="a_Category.php" class="btn-add">⬅ Retour</a>
        </div>

        <?php if ($error): ?>
            <p class="error">❌ <?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" id="addCategoryForm">
            <div class="section-box">

                <label for="nom">Nom de la catégorie</label>
                <input type="text" id="nom" name="nom" >

                <label>Description</label>
                <textarea id="description" name="description" ></textarea>

                <button class="btn-submit">Créer la Catégorie</button>

            </div>
        </form>

    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<!-- JS GLOBAL -->
<script src="../../assets/js/admin.js"></script>

<!-- JS PAGE -->
<script src="../assets/js/add_Category.js"></script>

</body>
</html>
