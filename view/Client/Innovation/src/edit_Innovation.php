<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireLogin();
?>

<?php
require_once __DIR__ . "/../../../../controller/components/Innovation/inns_Config.php";
if (!isset($_SESSION['user_id'])) {
    die("Utilisateur non connecté.");
}

$user_id = (int)$_SESSION['user_id'];

$innCtrl = new InnovationController();
$catCtrl = new CategoryController();
$categories = $catCtrl->listCategories();

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("ID invalide.");

$innovation = $innCtrl->getInnovation($id);
if (!$innovation || $innovation['user_id'] != $user_id) {
    die("Accès refusé.");
}

// ✅ TRAITEMENT UPDATE
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);
    $category_id = (int)$_POST["categorie_id"];

    $filePath = $innovation["file"]; // ancien fichier par défaut

    // ✅ NOUVEAU FICHIER ?
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === 0) {

        // SUPPRIMER ANCIEN FICHIER SI EXISTE
        if ($filePath) {
            $oldPath = $_SERVER["DOCUMENT_ROOT"] . "/projet-web/" . ltrim($filePath, "/");
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $uploadDir = $_SERVER["DOCUMENT_ROOT"] . "/projet-web/view/Client/Innovation/uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $allowed = ["png","jpg","jpeg","gif","webp","pdf","zip"];

        if (in_array($ext, $allowed)) {
            $newName = uniqid("file_", true) . "." . $ext;
            move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDir . $newName);

            $filePath = "view/Client/Innovation/uploads/" . $newName;
        }
    }

    // ✅ UPDATE + REPASSER EN ATTENTE
    $sql = "UPDATE innovations SET 
                titre = :titre,
                description = :description,
                category_id = :cat,
                file = :file,
                statut = 'En attente'
            WHERE id = :id";

    $stmt = config::getConnexion()->prepare($sql);
    $stmt->execute([
        ":titre" => $titre,
        ":description" => $description,
        ":cat" => $category_id,
        ":file" => $filePath,
        ":id" => $id
    ]);

    header("Location: list_Innovation.php?user=" . urlencode($_SESSION['user_id']) . "&msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Innovation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/add_innovation.css">

</head>
<body>
<div class="bg-animation">
    <div class="neural-network"></div>
    <div class="particles"></div>
</div>

<header class="cs-header">
    <div class="cs-container">
        <a href="../../index.php" class="cs-logo">Innovation</a>
        <nav class="cs-nav">
            <a href="../../index.php">Accueil</a>
            <a href="categories.php">Catégories</a>
            <a href="add_Innovation.php">Ajouter une Innovation</a>
            <a href="list_Innovation.php?user=<?= urlencode($_SESSION['user_id']) ?>">Mes innovations</a>
        </nav>
    </div>
</header>


<section class="cs-section">
    <div class="cs-form-wrapper">

        <h2 class="cs-form-title">Modifier l'innovation</h2>
        <form id="innovationForm" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="titre">Titre de l’innovation :</label>
                <input type="text" id="titre" name="titre"
                       value="<?= htmlspecialchars($innovation['titre']) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="categorie_id">Catégorie :</label>
                <select id="categorie_id" name="categorie_id" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= $innovation['category_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($innovation['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Fichier actuel :</label>
                <?php if ($innovation["file"]): ?>
                    <p class="current-file">
                        <?= htmlspecialchars(basename($innovation["file"])) ?>
                    </p>
                <?php else: ?>
                    <p class="current-file">Aucun fichier</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="file">Remplacer le fichier :</label>
                <input type="file" id="file" name="file">
            </div>

            <div class="form-actions">

                <!-- ✅ Bouton Mise à jour -->
                <button type="submit" class="cs-btn-gradient">
                    Mettre à jour
                </button>

                <!-- ✅ Bouton Annuler EN DESSOUS -->
                <a href="list_Innovation.php?user=<?= urlencode($_SESSION['user_id']) ?>"
                   class="cs-btn-cancel cs-btn-cancel-bottom">
                    Annuler
                </a>

            </div>


        </form>

    </div>
</section>

<script src="../assets/js/list_innovation.js"></script>

</body>
</html>
