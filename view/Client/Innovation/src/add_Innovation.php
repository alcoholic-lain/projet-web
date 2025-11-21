<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$innCtrl = new InnovationController();
$catCtrl = new CategoryController();

$categories = $catCtrl->listCategories();

// Traitement PHP uniquement si le JS a validé (validated = 1)
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["validated"] ?? "0") === "1") {

    $titre        = trim($_POST["titre"] ?? "");
    $description  = trim($_POST["description"] ?? "");
    $categorie_id = (int)($_POST["categorie_id"] ?? 0);
    $innovation = new Innovation(
            null,
            $titre,
            $description,
            $categorie_id,
            66,     // ← ICI tu mets un user_id existant
            "En attente"
    );

    $innCtrl->addInnovation($innovation);
    header("Location: list_Innovation.php?msg=added");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <title>Soumettre une Innovation – Innovation DB</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Tailwind (pour la grille de base, textes, etc.) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Style spécifique page add innovation -->
    <link rel="stylesheet" href="../assets/css/add_innovation.css">
</head>

<body>

<!-- FOND ANIMÉ TYPE CHAIN SUMMIT -->
<div class="bg-animation">
    <div class="neural-network" id="neuralNetwork"></div>
    <div class="particles" id="particles"></div>
</div>

<!-- HEADER SOMBRE -->
<header class="cs-header">
    <div class="cs-container">
        <a href="../../index.php" class="cs-logo">Hichem Challakhi</a>
        <nav class="cs-nav">
            <a href="../../index.php">Accueil</a>
            <a href="categories.php">Catégories</a>
            <a href="list_Innovation.php">Innovations</a>
        </nav>
    </div>
</header>

<!-- HERO STYLE CHAIN SUMMIT -->
<section class="cs-hero">
    <div class="cs-hero-inner">
        <p class="cs-eyebrow">🚀 Espace Innovation</p>
        <h1 class="cs-hero-title">
            Soumettre une Innovation
        </h1>
        <p class="cs-hero-subtitle">
            Partagez votre idée et devenez un innovateur dans l’univers Tunispace.
        </p>
    </div>
</section>

<!-- FORMULAIRE DANS CARTE GLASS -->
<section class="cs-section">
    <div class="cs-form-wrapper">

        <!-- Bloc d’erreur géré par le JS -->
        <div id="error-box" class="cs-alert hidden"></div>

        <h2 class="cs-form-title">Formulaire de Soumission</h2>

        <form id="innovationForm" method="post">

            <!-- Flag pour dire au PHP que le JS a validé -->
            <input type="hidden" name="validated" id="validated" value="0">

            <label for="titre">Titre de l’innovation :</label>
            <input type="text" id="titre" name="titre"
                   placeholder="Ex : Propulsion ionique pour nano-satellites">

            <label for="categorie_id">Catégorie :</label>
            <select id="categorie_id" name="categorie_id">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="5"
                      placeholder="Décrivez votre innovation, l’objectif, la technologie, l’impact…"></textarea>

            <button type="submit" class="cs-btn-gradient">
                Envoyer l’innovation 🚀
            </button>
        </form>
    </div>
</section>

<footer class="cs-footer">
    &copy; 2025 – Add Innovation  – Tunispace – Hichem Challakhi
</footer>

<!-- JS : animations fond + validation -->
<script src="../assets/js/add_innovation.js"></script>
</body>
</html>
