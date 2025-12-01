<?php
require_once __DIR__ . "/../../controller/security.php";
requireAdmin();

?>

<?php
$pageTitle = "Dashboard Admin";
require_once __DIR__ . "/../../controller/components/Innovation/inns_Config.php";

$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

/* === STATS === */
$categories = $catCtrl->listCategories();
$innovations = $innCtrl->listInnovations();

$totalCategories   = count($categories);
$totalInnovations  = count($innovations);
$totalPending      = 0;

foreach ($innovations as $inn) {
    if ($inn['statut'] === 'En attente') {
        $totalPending++;
    }
}
/* === STATS FOR GRAPH === */
// 1. Tableau : id => nom
$categoryNames = [];
foreach ($categories as $c) {
    $categoryNames[$c['id']] = $c['nom'];
}
// 2. Initialiser toutes les catégories à 0 (même si elles sont vides)
$catCounts = [];
foreach ($categories as $c) {
    $catCounts[$c['nom']] = 0;
}
// 3. Ajouter les innovations existantes
foreach ($innovations as $inn) {
    $catId = $inn['category_id'];
    $catName = $categoryNames[$catId] ?? null;
    if ($catName) {
        $catCounts[$catName]++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin – Innovation</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/layout/sidebar.php"; ?>
<?php include __DIR__ . "/layout/header.php"; ?>
<!-- ==================== MAIN ==================== -->
<main>
    <div class="dashboard-inner">
        <!-- === SECTION BIENVENUE ADMIN === -->
        <h2 class="section-title-main">Bienvenue, Admin <?= htmlspecialchars($_SESSION['pseudo']) ?></h2>

        <p>
            <a href="../Client/login/logout.php" class="link-logout">Déconnexion</a>
        </p>

        <h2 class="section-title-main">Gestion du système</h2>
        <!-- Cards principales -->
        <div class="card">
            <div class="card-icon">👥</div>
            <h3>Admin</h3>
            <p>Dashboard de gestion des comptes (admin + équipe)</p>
            <a href="login/src/dashboard.php">Accéder</a>
        </div>

        <div class="cards-grid">
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
        <!-- Statistiques -->
        <section style="margin-top: 60px;">
            <h2 class="stats-title">📊 Statistiques du système</h2>
            <div class="cards-grid">
                <div class="card">
                    <h3>Total Innovations</h3>
                    <p style="font-size:32px; font-weight:bold; margin:10px 0 0;">
                        <?= $totalInnovations ?>
                    </p>
                </div>
                <div class="card">
                    <h3>Total Catégories</h3>
                    <p style="font-size:32px; font-weight:bold; margin:10px 0 0;">
                        <?= $totalCategories ?>
                    </p>
                </div>
                <div class="card">
                    <h3>En attente</h3>
                    <p style="font-size:32px; font-weight:bold; margin:10px 0 0;">
                        <?= $totalPending ?>
                    </p>
                </div>
            </div>
            <div class="chart-wrapper">
                <h3>Répartition des innovations par catégorie</h3>
                <canvas id="chartCats"></canvas>
            </div>
        </section>

    </div>
</main>

<!-- ==================== FOOTER ==================== -->
<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode(array_keys($catCounts)) ?>;
    const data   = <?= json_encode(array_values($catCounts)) ?>;

    const ctx = document.getElementById('chartCats');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Nombre d'innovations",
                data: data,
                backgroundColor: '#8A8DFF'
            }]
        }
    });
</script>

<script src="assets/js/admin.js"></script>

</body>
</html>