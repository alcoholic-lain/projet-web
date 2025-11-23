<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$catCtrl = new CategoryController();

// Récupération des catégories depuis la BDD
$categories = $catCtrl->listCategories();
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Catégories</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Tailwind (base) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS Galaxy Web3 -->
    <link rel="stylesheet" href="../assets/css/categories.css">
    <link rel="stylesheet" href="../../assets/css/user.css">

</head>
<body>
<?php include __DIR__ . "/../../navbar.php"; ?>

<!-- ===== CANVAS GALAXY ===== -->
<div class="bg-animation"></div>


<!-- ===== HERO ===== -->
<section class="cs-hero">
    <div class="cs-hero-inner">
        <p class="cs-eyebrow">📂 Explorer les domaines</p>
        <h1 class="cs-hero-title">
            Toutes les Catégories
        </h1>
        <p class="cs-hero-subtitle">
            Parcourez les univers d’innovation : énergie, robotique, habitats lunaires, exploration spatiale…
        </p>

        <!-- Barre de recherche -->
        <div class="cs-search-wrapper">
            <div class="cs-search-inner">
                <input
                        type="text"
                        id="search-input"
                        placeholder="Rechercher une catégorie..."
                        class="cs-search-input"
                />
                <button class="cs-search-btn" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION CATEGORIES ===== -->
<section class="cs-section">
    <div class="cs-categories-wrapper">

        <div class="cs-toolbar">
            <h2 class="cs-section-title">Catégories disponibles</h2>
            <div class="cs-view-toggle">
                <button id="grid-view" class="cs-toggle-btn cs-toggle-active" type="button">
                    <i class="fas fa-th"></i>
                </button>
                <button id="list-view" class="cs-toggle-btn" type="button">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- GRID VIEW -->
        <div id="categories-grid" class="cs-grid">
            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <article
                    class="cs-category-card"
                    data-name="<?= strtolower(htmlspecialchars($cat['nom'])) ?>"
            >
                <div class="cs-card-header">
                    <h3 class="cs-pill">
                        <i class="fas fa-galaxy"></i>
                        <?= htmlspecialchars($cat['nom']) ?>
                        </h3>
                </div>
                <p class="cs-card-desc">
                    <?= htmlspecialchars($cat['description'] ?: "Aucune description fournie.") ?>
                </p>
                <div class="cs-card-footer">
                            <span class="cs-date">
                                <?= htmlspecialchars($cat['date_creation']) ?>
                            </span>
                    <a href="list_Innovation.php?categorie=<?= urlencode($cat['id']) ?>"
                       class="cs-btn-link">
                        Voir les innovations
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
            <?php else: ?>
            <p class="cs-empty-text">Aucune catégorie trouvée.</p>
            <?php endif; ?>
        </div>

        <!-- LIST VIEW -->
        <div id="categories-list" class="cs-list hidden">
            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <article
                    class="cs-category-row"
                    data-name="<?= strtolower(htmlspecialchars($cat['nom'])) ?>"
            >
                <div>
                    <h3 class="cs-row-title"><?= htmlspecialchars($cat['nom']) ?></h3>
                    <p class="cs-row-desc">
                        <?= htmlspecialchars($cat['description'] ?: "Aucune description fournie.") ?>
                    </p>
                </div>
                <div class="cs-row-meta">
                    <span class="cs-date"><?= htmlspecialchars($cat['date_creation']) ?></span>
                    <a href="list_Innovation.php?categorie=<?= urlencode($cat['id']) ?>"
                       class="cs-btn-link">
                        Voir les innovations
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- EMPTY STATE -->
        <div id="empty-state" class="cs-empty hidden">
            <i class="fas fa-folder-open cs-empty-icon"></i>
            <h3>Aucune catégorie trouvée</h3>
            <p>Essaye un autre mot-clé ou vide le champ de recherche.</p>
        </div>

    </div>
</section>

<footer class="cs-footer">
    &copy; 2025 – Categories – Tunispace – Hichem Challakhi
</footer>

<!-- JS Galaxy + Search + Toggle -->
<script src="../assets/js/categories.js"></script>
</body>
</html>
