<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

$categories = $catCtrl->listCategories();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $statut = trim($_POST['statut']);
    $user_id = 66;

    if ($titre === "" || $description === "" || $category_id <= 0) {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {
        $innovation = new Innovation(
                null,
                $titre,
                $description,
                $category_id,
                $user_id,
                $statut,
                null
        );

        if ($innCtrl->addInnovation($innovation)) {
            header("Location: a_Innovation.php?msg=added");
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
    <title>Ajouter Innovation</title>

    <!-- CSS GLOBAL (layout + sidebar + header) -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE PAGE -->
    <link rel="stylesheet" href="../assets/css/add_Innovation.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR GLOBAL -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER GLOBAL -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <!-- TITRE DE LA PAGE -->
        <div class="page-header-row">
            <h2 class="section-title-main">🚀 + Ajouter une Innovation</h2>

            <a href="a_Innovation.php" class="btn-add">⬅ Retour</a>
        </div>

        <p>Créer un nouveau projet d’innovation</p>

        <!-- MESSAGE D’ERREUR -->
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- FORMULAIRE -->
        <form method="post">
            <div class="section-box">

                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" >

                <label for="description">Description</label>
                <textarea id="description" name="description" ></textarea>

                <label>Catégorie</label>
                <select name="category_id" required>
                    <option value="">— Choisir une catégorie —</option>

                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="En attente">En attente</option>
                    <option value="Validée">Validée</option>
                    <option value="Rejetée">Rejetée</option>
                </select>

                <button class="btn-submit">Créer Innovation</button>

            </div>
        </form>

    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<!-- JS GLOBAL -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE PAGE -->
<script src="../assets/js/add_Innovation.js"></script>

</body>
</html>
