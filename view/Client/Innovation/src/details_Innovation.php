<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";

$innCtrl = new InnovationController();
$catCtrl = new CategoryController();

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { die("ID innovation invalide."); }

$innovation = $innCtrl->getInnovation($id);
if (!$innovation) { die("Innovation introuvable."); }

$category = null;
if (!empty($innovation["category_id"])) {
    $category = $catCtrl->getCategory((int)$innovation["category_id"]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Innovation</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Tailwind (pour la grille de base, textes, etc.) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../assets/css/details_innovation.css">
</head>

<body>

<canvas id="galaxyCanvas"></canvas>
<!-- FOND ANIMÉ TYPE CHAIN SUMMIT -->
<div class="bg-animation">
    <div class="neural-network" id="neuralNetwork"></div>
    <div class="particles" id="particles"></div>
</div>

<header class="cs-header">
    <div class="cs-container">
        <a href="../../index.php" class="cs-logo">Innovation</a>
        <nav class="cs-nav">
            <a href="../../index.php">Accueil</a>
            <a href="categories.php">Catégories</a>
            <a href="add_Innovation.php">Ajouter une Innovation</a>
            <a href="list_Innovation.php?user=66">Mes innovations</a>
        </nav>
    </div>
</header>

<main class="cs-main">

    <!-- POST CARD STYLE -->
    <div class="post-card">

        <div class="post-category">
            <?= htmlspecialchars($category["nom"] ?? "Sans catégorie") ?>
        </div>

        <h1 class="post-title">
            <?= htmlspecialchars($innovation["titre"]) ?>
        </h1>

        <div class="post-meta">
            <span>📅 <?= htmlspecialchars($innovation["date_creation"]) ?></span>

            <?php
            $cls = "badge-pending";
            if ($innovation["statut"] === "Validée") $cls = "badge-valid";
            if ($innovation["statut"] === "Rejetée") $cls = "badge-refused";
            ?>
            <span class="badge <?= $cls ?>"><?= htmlspecialchars($innovation["statut"]) ?></span>
        </div>

        <p class="post-desc">
            <?= nl2br(htmlspecialchars($innovation["description"])) ?>
        </p>

        <a href="list_Innovation.php?categorie=<?= htmlspecialchars($innovation['category_id'] ?? '') ?>"
           class="post-back">
            ← Retour aux innovations
        </a>


    </div>
</main>

<footer class="cs-footer">
    &copy; 2025 - Tunispace Innovation
</footer>

<script src="../assets/js/details_innovation.js"></script>
</body>
</html>
