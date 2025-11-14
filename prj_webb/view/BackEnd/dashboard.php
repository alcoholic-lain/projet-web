<?php
session_start();

// Vérification rôle admin
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header('Location: ../../FrontOffice/login.html');
    exit;
}

require_once '../../config.php';

// Connexion à la DB et récupération des utilisateurs
$db = Config::getConnexion();
$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TuniSpace</title>
    <link rel="stylesheet" href="../FrontOffice/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Bienvenue, Admin <?= htmlspecialchars($_SESSION['pseudo']) ?></h1>
    <a href="../FrontOffice/login.html" class="btn-logout">Déconnexion</a>

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
                <td><?= $user['role_id'] == 1 ? 'Admin' : 'User' ?></td>
                <td><?= htmlspecialchars($user['planet']) ?></td>
                <td>
                    <a href="../../controller/edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <a href="../../controller/delete_user.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?');">🗑️ Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
