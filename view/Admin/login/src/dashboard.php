<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireAdmin();
?>
<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../model/login/role.php";

$db = Config::getConnexion();

// --- Récupérer tous les utilisateurs ---
$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Récupérer les rôles ---
$stmtRoles = $db->prepare("SELECT * FROM roles");
$stmtRoles->execute();
$rolesData = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
foreach ($rolesData as $roleRow) {
    $roles[$roleRow['id']] = $roleRow['nom'];
}

// --- Résumé des rôles ---
$roleSummary = [];
foreach ($users as $user) {
    $roleName = $roles[$user['role_id']] ?? 'Inconnu';
    if (!isset($roleSummary[$roleName])) $roleSummary[$roleName] = 0;
    $roleSummary[$roleName]++;
}

$pageTitle     = "👥 Dashboard Utilisateurs";
$pageSubtitle  = "Gestion des comptes et rôles";
$activeMenu    = 'users';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- CSS GLOBAL ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- COPIE EXACTE DU STYLE CATÉGORIE -->
    <link rel="stylesheet" href="../assets/css/a_Category.css">

    <!-- CSS SPÉCIFIQUE UTILISATEURS (si tu en veux un plus tard) -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <!-- ====================== RÉSUMÉ DES RÔLES ====================== -->
        <h2 class="section-title-main">Résumé des rôles</h2>

        <section class="section-box">
            <table>
                <thead>
                <tr>
                    <th>Rôle</th>
                    <th>Nombre d'utilisateurs</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roleSummary as $roleName => $total): ?>
                    <tr>
                        <td><?= htmlspecialchars($roleName) ?></td>
                        <td><?= $total ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- ====================== LISTE DES UTILISATEURS ====================== -->
        <h2 class="section-title-main">Liste des utilisateurs</h2>

        <section class="section-box">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Pseudo</th>
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
                        <td><?= htmlspecialchars($user['pseudo']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['statut']) ?></td>
                        <td><?= htmlspecialchars($roles[$user['role_id']] ?? 'Inconnu') ?></td>
                        <td><?= htmlspecialchars($user['planet']) ?></td>

                        <td>
                            <a href="../../../../controller/components/login/edit_user.php?id=<?= $user['id'] ?>"
                               class="btn-icon edit"
                               title="Modifier">
                                ✏️
                            </a>

                            <a href="../../../../controller/components/login/delete_user.php?id=<?= $user['id'] ?>"
                               class="btn-icon delete"
                               onclick="return confirm('Supprimer cet utilisateur ?');"
                               title="Supprimer">
                                🗑️
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <p style="margin-top:18px;">
            <a href="../../../../controller/components/login/create_role.php" class="btn-add">
                 Créer un nouveau rôle
            </a>
        </p>

        <!-- ====================== GRAPHIQUES ====================== -->
        <h2 class="section-title-main">Historique des connexions (7 derniers jours)</h2>
        <section class="section-box">
            <canvas id="connexionsChart"></canvas>
        </section>

        <h2 class="section-title-main">Historique des modifications</h2>
        <section class="section-box">
            <canvas id="modificationsChart"></canvas>
        </section>


    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE DASHBOARD -->
<script src="../assets/js/dashboard.js"></script>

</body>
</html>
