<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header('Location: ../../FrontOffice/login.html');
    exit;
}

require_once '../../config.php';
require_once '../../model/Role.php';

$db = Config::getConnexion();

// --- Récupérer tous les utilisateurs ---
$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Récupérer tous les rôles dans un tableau id => nom ---
$stmtRoles = $db->prepare("SELECT * FROM roles");
$stmtRoles->execute();
$rolesData = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
foreach ($rolesData as $roleRow) {
    $roles[$roleRow['id']] = $roleRow['nom'];
}

// --- Calcul du nombre d'utilisateurs par rôle ---
$roleSummary = [];
foreach ($users as $user) {
    $roleName = $roles[$user['role_id']] ?? 'Inconnu';
    if (!isset($roleSummary[$roleName])) $roleSummary[$roleName] = 0;
    $roleSummary[$roleName]++;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../FrontOffice/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Bienvenue, Admin <?= htmlspecialchars($_SESSION['pseudo']) ?></h1>
    <a href="../FrontOffice/login.html" class="btn-logout">Déconnexion</a>

    <h2>Résumé des rôles</h2>
    <table>
        <thead>
        <tr><th>Rôle</th><th>Nombre d'utilisateurs</th></tr>
        </thead>
        <tbody>
        <?php foreach($roleSummary as $roleName => $total): ?>
            <tr>
                <td><?= htmlspecialchars($roleName) ?></td>
                <td><?= $total ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Liste des utilisateurs</h2>
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
        <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['pseudo']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['statut']) ?></td>
                <td><?= htmlspecialchars($roles[$user['role_id']] ?? 'Inconnu') ?></td>
                <td><?= htmlspecialchars($user['planet']) ?></td>
                <td>
                    <a href="../../controller/edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <a href="../../controller/delete_user.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?');">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="../../controller/create_role.php">Créer un nouveau rôle</a></p>
</div>
</body>
</html>
