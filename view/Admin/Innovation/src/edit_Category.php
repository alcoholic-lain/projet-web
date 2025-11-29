<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireAdmin();
?>

<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$catCtrl = new CategoryController();

// Récupération ID
$id = intval($_GET["id"] ?? 0);

// Récupération catégorie existante
$data = $catCtrl->getCategory($id);

if (!$data) {
    die("⚠️ Catégorie introuvable.");
}

$error = null;

// Soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);

    if ($nom === "" || $description === "") {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {

        $category = new Category(
                $id,
                $nom,
                $description,
                $data["date_creation"]
        );

        try {
            $catCtrl->updateCategory($category);
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

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE -->
    <link rel="stylesheet" href="../assets/css/edit_Category.css">
</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <div class="page-header-row">
            <h2 class="section-title-main">🗂️ Modifier la Catégorie</h2>
            <a href="a_Category.php" class="btn-add">⬅ Retour</a>
        </div>

        <?php if ($error): ?>
            <p class="error">❌ <?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="section-box">

                <label for="nom">Nom de la catégorie</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($data['nom']) ?>" >

                <label>Description</label>
                <textarea id="description" name="description"><?=
                    htmlspecialchars($data['description']) ?></textarea>

                <button class="btn-submit">Mettre à jour</button>

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
<script src="../assets/js/edit_Category.js"></script>

</body>
</html>
