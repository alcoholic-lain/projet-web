<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../model/Innovation/Category.php";
require_once __DIR__ . "/../../model/Innovation/Innovation.php";


$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

/* === STATS === */
$categories = $catCtrl->listCategories();
$innovations = $innCtrl->listInnovations();

$totalCategories = count($categories);
$totalInnovations = count($innovations);
$totalPending = 0;

foreach ($innovations as $inn) {
if ($inn['statut'] === 'En attente') {
$totalPending++;
}
}

/* === STATS FOR GRAPH === */
$catCounts = [];
foreach ($innovations as $inn) {
$cat = $inn['category_id'];
if (!isset($catCounts[$cat])) $catCounts[$cat] = 0;
$catCounts[$cat]++;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Innovation </title>
    <link rel="stylesheet" href="assets/css/admin.css">

</head>

<body class="admin-dashboard with-sidebar">

<div class="sidebar">
    <h2>🚀 Admin</h2>

    <a href="index.php" style="color:#FFB347; font-weight:bold;">
        <span class="icon">🏠</span>
        <span class="text">Dashboard</span>
    </a>

    <a href="Innovation/src/a_Category.php">
        <span class="icon">🗂️</span>
        <span class="text">Catégories</span>
    </a>

    <a href="Innovation/src/a_Innovation.php">
        <span class="icon">🚀</span>
        <span class="text">Innovations</span>
    </a>

    <a href="../Client/index.php">
        <span class="icon">🌐</span>
        <span class="text">Front Office</span>
    </a>
</div>

<header>
    <h1>🚀 Espace Administrateur</h1>
    <p style="margin-top: 10px; opacity: 0.9;">Tableau de bord - Innovation </p>
    <nav>
        <a href="../Client/index.php">Retour au Front Office</a>
    </nav>
</header>

<main class="dashboard">
    <h2>Gestion du système</h2>

    <div class="cards-container">
        <div class="card">
            <div class="card-icon">🗂️</div>
            <h3>Catégories</h3>
            <p>Créer, modifier et supprimer des catégories</p>
            <a href="Innovation/src/a_Category.php">Accéder</a>
        </div>

        <div class="card">
            <div class="card-icon">🚀</div>
            <h3>Innovations</h3>
            <p>Gérer les innovations soumises par les utilisateurs</p>
            <a href="Innovation/src/a_Innovation.php">Accéder</a>
        </div>
    </div>

    <section class="section-box" style="margin-top:60px;">
        <h2 style="text-align:center; color:#FFB347;">📊 Statistiques du système</h2>

        <div class="cards-container" style="margin-top:35px;">

            <div class="card">
                <h3>Total Innovations</h3>
                <p style="font-size:32px; font-weight:bold;">
                    <?= $totalInnovations ?>
                </p>
            </div>

            <div class="card">
                <h3>Total Catégories</h3>
                <p style="font-size:32px; font-weight:bold;">
                    <?= $totalCategories ?>
                </p>
            </div>

            <div class="card">
                <h3>En attente</h3>
                <p style="font-size:32px; font-weight:bold;">
                    <?= $totalPending ?>
                </p>
            </div>

        </div>

        <!-- === GRAPHIQUE === -->
        <div style="max-width:800px; margin:60px auto;">
            <h3 style="text-align:center; color:#8A8DFF;">
                Répartition des innovations par catégorie
            </h3>

            <canvas id="chartCats" style="margin-top:25px;"></canvas>
        </div>

    </section>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode(array_keys($catCounts)) ?>;
    const data = <?= json_encode(array_values($catCounts)) ?>;

    const ctx = document.getElementById('chartCats');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre d\'innovations',
                data: data,
                backgroundColor: '#8A8DFF'
            }]
        }
    });
</script>
<script src="assets/js/admin.js"></script>

</body>
</html>
