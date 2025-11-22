<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

// Récupérer ID
$id = intval($_GET["id"] ?? 0);

// Récupération innovation existante
$data = $innCtrl->getInnovation($id);

if (!$data) {
    die("⚠️ Innovation introuvable.");
}

// Récupération catégories
$categories = $catCtrl->listCategories();

$error = null;

// Soumission formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);
    $categorie = intval($_POST["category_id"]);
    $statut = trim($_POST["statut"]);
    $user_id = $data["user_id"]; // garder user original

    if ($titre === "" || $description === "" || $categorie <= 0) {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {

        $innovation = new Innovation(
                $id,
                $titre,
                $description,
                $categorie,
                $user_id,
                $statut,
                $data["date_creation"]
        );

        try {
            $innCtrl->updateInnovation($innovation);
            header("Location: a_Innovation.php?msg=updated");
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
    <title>Modifier Innovation</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE -->
    <link rel="stylesheet" href="../assets/css/edit_Innovation.css">

</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR GLOBAL -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER GLOBAL -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <div class="page-header-row">
            <h2 class="section-title-main">✏️ Modifier l’Innovation</h2>
            <a href="a_Innovation.php" class="btn-add">⬅ Retour</a>
        </div>

        <?php if ($error): ?>
            <p class="error">❌ <?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="section-box">

                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre"
                       value="<?= htmlspecialchars($data['titre']) ?>" >

                <label>Description</label>
                <textarea id="description" name="description" ><?=
                    htmlspecialchars($data['description']) ?></textarea>

                <label>Catégorie</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                <?= ($cat['id'] == $data['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <label for="statut">Statut</label>
                <select name="statut">
                    <option value="En attente" <?= $data["statut"]=="En attente"?"selected":"" ?>>En attente</option>
                    <option value="Validée"   <?= $data["statut"]=="Validée"?"selected":"" ?>>Validée</option>
                    <option value="Rejetée"   <?= $data["statut"]=="Rejetée"?"selected":"" ?>>Rejetée</option>
                </select>

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

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/edit_Innovation.js"></script>

</body>
</html>
