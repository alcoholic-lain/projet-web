<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 🔹 Restaurer la session depuis les cookies si session vide (Remember Me)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['pseudo'] = $_COOKIE['user_pseudo'] ?? '';
    $_SESSION['role_id'] = $_COOKIE['role_id'] ?? '';
}

// 🔹 Redirection vers login si pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../view/LoginC/login.html");
    exit;
}

require_once '../../../config.php';

$db = Config::getConnexion();

// 🔹 Récupération des utilisateurs
$stmt = $db->prepare("SELECT * FROM user ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Rôles
$stmtRoles = $db->prepare("SELECT * FROM roles");
$stmtRoles->execute();
$roles = [];
foreach ($stmtRoles->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $roles[$row['id']] = $row['nom'];
}

// 🔹 Récap par rôle
$roleSummary = [];
foreach ($users as $u) {
    $r = $roles[$u['role_id']] ?? "Inconnu";
    if (!isset($roleSummary[$r])) $roleSummary[$r] = 0;
    $roleSummary[$r]++;
}

// 🔹 Statistiques 7 jours
$days = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date("Y-m-d", strtotime("-$i day"));
    $days[$day] = 0;
}

// 🔹 Connexions
$stmt = $db->prepare("
    SELECT DATE(created_at) AS day, COUNT(*) AS total
    FROM user_activity
    WHERE action = 'connexion'
    AND created_at >= DATE(NOW()) - INTERVAL 7 DAY
    GROUP BY DATE(created_at)
");
$stmt->execute();
$loginData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$loginCounts = $days;
foreach ($loginData as $row) {
    $loginCounts[$row['day']] = (int)$row['total'];
}

// 🔹 Modifications
$stmt = $db->prepare("
    SELECT DATE(created_at) AS day, COUNT(*) AS total
    FROM user_activity
    WHERE action = 'modification'
    AND created_at >= DATE(NOW()) - INTERVAL 7 DAY
    GROUP BY DATE(created_at)
");
$stmt->execute();
$modifData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$modCounts = $days;
foreach ($modifData as $row) $modCounts[$row['day']] = (int)$row['total'];

// 🔹 JSON pour JS
$connLabels = json_encode(array_keys($loginCounts));
$connValues = json_encode(array_values($loginCounts));
$modLabels = json_encode(array_keys($modCounts));
$modValues = json_encode(array_values($modCounts));
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Utilisateurs & Rôles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../LoginC/assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../layout/header.php"; ?>

<!-- MAIN -->
<main>
    <div class="container">
        <h1 class="page-title">🚀 Tableau de bord administrateur</h1>

        <!-- Résumé des rôles -->
        <div class="cards-grid">
            <?php foreach ($roleSummary as $role => $count): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($role) ?></h3>
                    <p style="font-size: 32px; font-weight: bold; margin: 10px 0;">
                        <?= $count ?> utilisateur<?= $count > 1 ? 's' : '' ?>
                    </p>
                </div>
            <?php endforeach; ?>
            <div class="card">
                <h3>Total utilisateurs</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #b38cff;">
                    <?= count($users) ?>
                </p>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="cards-grid">
            <div class="card">
                <h3>📊 Connexions (7 jours)</h3>
                <canvas id="connexionsChart" height="100"></canvas>
            </div>
            <div class="card">
                <h3>✍️ Modifications profil (7 jours)</h3>
                <canvas id="modificationsChart" height="100"></canvas>
            </div>
        </div>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="🔍 Rechercher par pseudo, email ou rôle..." />
        </div>

        <!-- Liste des utilisateurs -->
        <h2 style="margin: 40px 0 20px; color: var(--accent);">👥 Liste complète des utilisateurs</h2>
        <div class="table-container">
            <table id="userTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th id="colPseudo">Pseudo</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Rôle</th>
                    <th>Planète</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><strong><?= htmlspecialchars($user['pseudo']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['statut']) ?></td>
                        <td><?= htmlspecialchars($roles[$user['role_id']] ?? 'Inconnu') ?></td>
                        <td><?= htmlspecialchars($user['planet'] ?? '-') ?></td>
                        <td>
                            <a href="../../../controller/UserC/edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">✏️ Modifier</a>
                            <a href="../../../controller/UserC/delete_user.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?');">🗑️ Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


</main>



<script>
        // Passer les données PHP au JS global
        const connLabels = <?= $connLabels ?>;
        const connValues = <?= $connValues ?>;
        const modLabels = <?= $modLabels ?>;
        const modValues = <?= $modValues ?>;

</script>
<script src="../../LoginC/assets/js/dashboard.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>