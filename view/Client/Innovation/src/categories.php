<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$catCtrl = new CategoryController();
$categories = $catCtrl->listCategories();
?>
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Catégories</title>

    <!-- CSS du header -->
    <link rel="stylesheet" href="../../assets/css/user.css">

    <!-- CSS des catégories -->
    <link rel="stylesheet" href="../assets/css/categories.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>

<body class="dark">

<!-- 🌌 FOND ANIMÉ -->
<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<!-- ⭐ HEADER INDEX.HTML IDENTIQUE -->
<header>
    <div class="container">
        <h1>Tunispace</h1>

        <nav>
            <a href="../../../Client/index.html">Home</a>
            <a href="#" class="active">Categories</a>
            <a href="#messages">Messages</a>
            <a href="../../Reclamation/src/choix.php">Reclamation</a>

        </nav>

        <div style="position:relative">
            <img id="user-avatar" src="https://randomuser.me/api/portraits/men/86.jpg" alt="You">

            <div id="user-dropdown">
                <div class="dropdown-item"><i class="fas fa-user"></i> My Profile</div>

                <div class="dropdown-item">
                    <i class="fas fa-moon"></i> Dark Mode
                    <label class="ml-auto">
                        <input type="checkbox" id="theme-switch" class="toggle-checkbox" checked>
                        <span class="toggle-label"></span>
                    </label>
                </div>

                <div class="dropdown-item"><i class="fas fa-bell"></i> Notifications</div>
                <div class="dropdown-item"><i class="fas fa-cog"></i> Settings</div>

                <hr class="border-gray-700 my-2">

                <div class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Logout</div>
            </div>
        </div>
    </div>
</header>


<!-- ===== HERO ===== -->
<section class="cs-hero">
    <div class="cs-hero-inner">
        <p class="cs-eyebrow">📂 Explorer les domaines</p>
        <h1 class="cs-hero-title">Toutes les Catégories</h1>

        <p class="cs-hero-subtitle">
            Parcourez les univers d’innovation : énergie, robotique, habitats lunaires,
            exploration spatiale…
        </p>

        <!-- Barre de recherche -->
        <div class="cs-search-wrapper">
            <div class="cs-search-inner">
                <input type="text" id="search-input" placeholder="Rechercher une catégorie..." class="cs-search-input">
                <button class="cs-search-btn" type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
</section>



<!-- ===== SECTION LISTE DES CATÉGORIES ===== -->
<section class="cs-section">
    <div class="cs-categories-wrapper">

        <div class="cs-toolbar">
            <h2 class="cs-section-title">Catégories disponibles</h2>

            <div class="cs-view-toggle">
                <button id="grid-view" class="cs-toggle-btn cs-toggle-active"><i class="fas fa-th"></i></button>
                <button id="list-view" class="cs-toggle-btn"><i class="fas fa-list"></i></button>
            </div>
        </div>


        <!-- Affichage Grille -->
        <div id="categories-grid" class="cs-grid">
            <?php foreach ($categories as $cat): ?>
                <article class="cs-category-card" data-name="<?= strtolower(htmlspecialchars($cat['nom'])) ?>">
                    <div class="cs-card-header">
                        <h3 class="cs-pill"><?= htmlspecialchars($cat['nom']) ?></h3>
                    </div>

                    <p class="cs-card-desc">
                        <?= htmlspecialchars($cat['description'] ?: "Aucune description fournie.") ?>
                    </p>

                    <div class="cs-card-footer">
                        <span class="cs-date"><?= htmlspecialchars($cat['date_creation']) ?></span>

                        <a href="list_Innovation.php?categorie=<?= $cat['id'] ?>" class="cs-btn-link">
                            Voir les innovations <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>


        <!-- Affichage Liste -->
        <div id="categories-list" class="cs-list hidden">
            <?php foreach ($categories as $cat): ?>
                <article class="cs-category-row" data-name="<?= strtolower(htmlspecialchars($cat['nom'])) ?>">
                    <div>
                        <h3 class="cs-row-title"><?= htmlspecialchars($cat['nom']) ?></h3>
                        <p class="cs-row-desc"><?= htmlspecialchars($cat['description'] ?: "Aucune description fournie.") ?></p>
                    </div>

                    <div class="cs-row-meta">
                        <span class="cs-date"><?= htmlspecialchars($cat['date_creation']) ?></span>

                        <a href="list_Innovation.php?categorie=<?= $cat['id'] ?>" class="cs-btn-link">
                            Voir les innovations <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>


        <!-- Aucun résultat -->
        <div id="empty-state" class="cs-empty hidden">
            <i class="fas fa-folder-open cs-empty-icon"></i>
            <h3>Aucune catégorie trouvée</h3>
            <p>Essaye un autre mot-clé ou vide le champ de recherche.</p>
        </div>

    </div>
</section>


<!-- BUTTON MESSAGES -->
<div id="dm-btn"><i class="fas fa-comment-dots"></i></div>

<!-- POPUP MESSAGES -->
<div id="dm-popup">
    <div class="chat-header">
        <div class="chat-header-left">
            <button id="back-to-list"
                    style="display:none;background:none;border:none;color:white;font-size:18px;cursor:pointer;opacity:0.9">
                <i class="fas fa-arrow-left"></i>
            </button>

            <img id="popup-avatar" src="" alt="" style="opacity:0">

            <div class="chat-info">
                <h3 id="popup-name">Messages</h3>
                <p id="popup-status"></p>
            </div>
        </div>

        <div class="header-buttons">
            <button id="minimize-btn"><i class="fas fa-minus"></i></button>
            <button id="maximize-btn"><i class="fas fa-expand"></i></button>
            <button id="close-popup"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="popup-body">
        <div class="conversations-panel" id="conversations-panel">
            <div class="search-section">
                <input type="text" id="search-input" placeholder="Search contacts...">
            </div>

            <div class="contacts-list">
                <div class="contact-item" data-contact="sarah">
                    <img src="https://randomuser.me/api/portraits/women/32.jpg">
                    <div class="info">
                        <div class="name">Sarah Johnson</div>
                        <div class="preview">Sounds good!</div>
                    </div>
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                </div>

                <div class="contact-item" data-contact="mike">
                    <img src="https://randomuser.me/api/portraits/men/45.jpg">
                    <div class="info">
                        <div class="name">Mike Chen</div>
                        <div class="preview">Meeting at 3 PM</div>
                    </div>
                </div>

                <div class="contact-item" data-contact="emma">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg">
                    <div class="info">
                        <div class="name">Emma Davis</div>
                        <div class="preview">Thanks!</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-panel hidden" id="chat-panel">
            <div id="popup-messages"></div>

            <div class="input-bar">
                <button><i class="fas fa-paperclip"></i></button>
                <input type="text" id="popup-input" placeholder="Type a message...">
                <button id="popup-send"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="cs-footer">
    &copy; 2025 – Categories – Tunispace – Hichem Challakhi
</footer>
<!-- JS -->
<script src="../assets/js/categories.js"></script>
<script src="../../assets/js/user.js"></script>

</body>
</html>
