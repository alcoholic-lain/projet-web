<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../model/login/role.php";

$db = Config::getConnexion();

// ==============================
// RÉCUPÉRATION DES UTILISATEURS
// ==============================
$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// RÉCUPÉRATION DES RÔLES
// ==============================
$stmtRoles = $db->prepare("SELECT * FROM roles");
$stmtRoles->execute();
$rolesData = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
foreach ($rolesData as $roleRow) {
    $roles[$roleRow['id']] = $roleRow['nom'];
}

// Résumé des rôles
$roleSummary = [];
foreach ($users as $user) {
    $roleName = $roles[$user['role_id']] ?? 'Inconnu';
    if (!isset($roleSummary[$roleName])) $roleSummary[$roleName] = 0;
    $roleSummary[$roleName]++;
}

// ==============================
// RÉCUPÉRATION DES RÉCLAMATIONS
// ==============================
$stmtRecl = $db->prepare("SELECT * FROM reclamations");
$stmtRecl->execute();
$reclamations = $stmtRecl->fetchAll(PDO::FETCH_ASSOC);

// Sécurité si rien récupéré
if (!is_array($reclamations)) {
    $reclamations = [];
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

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>

    <!-- Statistiques Réclamations -->
    <div class="dashboard-stats">
        <?php
        $total = count($reclamations);
        $en_attente = 0;
        $confirme = 0;
        $rejete = 0;

        foreach ($reclamations as $reclamation) {
            switch ($reclamation['statut']) {
                case 'en attente': 
                    $en_attente++; 
                    break;
                case 'confirmé': 
                    $confirme++; 
                    break;
                case 'rejeté': 
                    $rejete++; 
                    break;
            }
        }
        ?>
        
        <div class="stat-card">
            <h3>Total</h3>
            <div class="stat-number"><?= $total ?></div>
            <p>Réclamations</p>
        </div>

        <div class="stat-card">
            <h3>En attente</h3>
            <div class="stat-number" style="color:#FFB347;"><?= $en_attente ?></div>
            <p>En traitement</p>
        </div>

        <div class="stat-card">
            <h3>Confirmées</h3>
            <div class="stat-number" style="color:#4AFF8B;"><?= $confirme ?></div>
            <p>Validées</p>
        </div>

        <div class="stat-card">
            <h3>Rejetées</h3>
            <div class="stat-number" style="color:#FF6B6B;"><?= $rejete ?></div>
            <p>Refusées</p>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="liste.php" class="btn-dashboard">📄 Voir Toutes les Réclamations</a>
    </div>

</main>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE DASHBOARD -->
<script src="../assets/js/dashboard.js"></script>

</body>
</html>