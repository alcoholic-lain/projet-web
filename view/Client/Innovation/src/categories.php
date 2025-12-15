<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireLogin();
require_once __DIR__ . "/../../../../controller/components/Innovation/inns_Config.php";

$username = $_SESSION['pseudo'] ?? 'User';

// ================= AVATAR =================
// Avatar par défaut
$defaultAvatar = 'view/Client/uploads/avatars/default.jpg';

// Récupération de l’avatar depuis session
$avatar = $_SESSION['avatar'] ?? $defaultAvatar;

// Nettoyage du chemin
$avatar = ltrim($avatar, '/');

// Chemin serveur pour vérifier existence
$avatarPath = $_SERVER['DOCUMENT_ROOT'] . '/projet-web/' . $avatar;

// Si le fichier n’existe pas, mettre avatar par défaut
if (!file_exists($avatarPath)) {
    $avatar = $defaultAvatar;
}

// URL publique pour <img src="">
$avatarUrl = '/projet-web/' . $avatar;

// ================= INNOVATIONS =================
$innovationCtrl = new InnovationController();
$userId = $_SESSION['user_id'];
$innovationCount = $innovationCtrl->countInnovationsByUser($userId);

// ================= CATEGORIES =================
$catCtrl = new CategoryController();
$categories = $catCtrl->listCategories();
?>
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Catégories</title>

    <link rel="stylesheet" href="../../assets/css/user.css">
    <link rel="stylesheet" href="../assets/css/categories.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="dark">

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <div class="container">
        <h1>TuniSpace</h1>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="#Innovation/src/categories.php" class="active">Catégorie</a>
            <a href="#messages">Messages</a>
            <a href="../../Reclamation/src/choix.php">Réclamation</a>
        </nav>

        <div style="position:relative">
            <img id="user-avatar"
                 src="<?= htmlspecialchars($avatarUrl) ?>"
                 alt="<?= htmlspecialchars($username) ?>"
                 style="cursor:pointer; width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,127,42,.6); box-shadow:0 0 20px rgba(255,127,42,.4); transition: transform 0.25s;">

            <div id="user-dropdown">
                <div class="dropdown-item"
                     onclick="window.location.href='../../profile.php';"
                     style="cursor:pointer;">
                    <?= htmlspecialchars($username) ?>
                </div>

                <div class="dropdown-item">
                    Dark Mode
                    <label class="ml-auto">
                        <input type="checkbox" id="theme-switch" class="toggle-checkbox" checked>
                        <span class="toggle-label"></span>
                    </label>
                </div>

                <div class="dropdown-item">Notifications</div>
                <div class="dropdown-item">Settings</div>

                <hr class="border-gray-700 my-2">

                <div class="dropdown-item logout">
                    <a href="/projet-web/logout.php" style="color:#ff002d;text-decoration:none;">Se déconnecter</a>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="cs-hero">
    <div class="cs-hero-inner">
        <p class="cs-eyebrow">📂 Explorer les domaines</p>
        <h1 class="cs-hero-title">Toutes les Catégories</h1>
        <p class="cs-hero-subtitle">Parcourez les univers d’innovation : énergie, robotique, habitats lunaires, exploration spatiale…</p>
    </div>
    <div class="hero-action-bar">
        <a href="/projet-web/view/Client/Innovation/src/add_Innovation.php" class="hero-btn">
            <i class="fa-solid fa-plus"></i> Nouvelle Innovation
        </a>

        <a href="/projet-web/view/Client/Innovation/src/list_Innovation.php?user=<?= $_SESSION['user_id'] ?>" class="hero-btn">
            <i class="fa-solid fa-folder-open"></i> Mes Innovations
            <span class="hero-counter"><?= $innovationCount ?></span>
        </a>
    </div>
</section>

<div class="page-layout">
    <div class="left-content">
        <section class="cs-section">
            <div class="cs-categories-wrapper">
                <div class="cs-toolbar">
                    <h2 class="cs-section-title">Catégories disponibles</h2>
                    <div class="cs-view-toggle">
                        <button id="grid-view" class="cs-toggle-btn cs-toggle-active"><i class="fas fa-th"></i></button>
                        <button id="list-view" class="cs-toggle-btn"><i class="fas fa-list"></i></button>
                    </div>
                </div>

                <div id="categories-grid" class="cs-grid">
                    <?php foreach ($categories as $cat): ?>
                        <article class="cs-category-card" data-name="<?= strtolower(htmlspecialchars($cat['nom'])) ?>">
                            <div class="cs-card-header">
                                <h3 class="cs-pill"><?= htmlspecialchars($cat['nom']) ?></h3>
                            </div>
                            <p class="cs-card-desc"><?= htmlspecialchars($cat['description'] ?: "Aucune description fournie.") ?></p>
                            <div class="cs-card-footer">
                                <span class="cs-date"><?= htmlspecialchars($cat['date_creation']) ?></span>
                                <a href="list_Innovation.php?categorie=<?= $cat['id'] ?>" class="cs-btn-link">
                                    Voir les innovations <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <div class="right-content">
        <div class="ai-box">
            <div class="ai-header">
                <span class="ai-icon">🤖</span>
                <p class="ai-desc">Pose une question, je trouve les catégories & innovations.</p>
            </div>

            <div id="aiChat" class="ai-chat"></div>

            <div class="ai-input-container">
                <input type="text" id="aiInput" placeholder="Ex : drones, énergie solaire, IA..." />
                <button id="aiSend">➤</button>
            </div>
        </div>

        <h2 class="ai-results-title">Résultats </h2>
        <div id="aiResultsBox" class="ai-results-box hidden">
            <h3 class="result-title">Catégories correspondantes</h3>
            <div id="aiCategories" class="result-grid"></div>

            <h3 class="result-title">Innovations correspondantes</h3>
            <div id="aiInnovations" class="result-grid"></div>

            <div id="aiNoResults" class="no-result hidden">
                ❌ Aucun résultat trouvé. Essaie un autre mot-clé !
            </div>
        </div>
    </div>
</div>

<footer class="cs-footer">
    &copy; 2025 – Catégories – TuniSpace – Hichem Challakhi
</footer>

<script src="../assets/js/categories.js"></script>
<script src="../../assets/js/user.js"></script>
</body>
</html>
