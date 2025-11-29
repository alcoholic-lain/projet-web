<?php
session_start();

// Vérification session et rôle admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../view/Client/login.html');
    exit;
}

require_once '../../../config.php';
require_once '../../../model/login/role.php';

$db = Config::getConnexion();

$planets = ['Mars', 'Terre', 'Jupiter', 'Venus'];
$statuses = ['Actif', 'Banni', 'Inactif'];

// Récupération de l'utilisateur
$user = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Utilisateur introuvable !");
    }
}

// Bloquer modification si utilisateur est admin
$readonly = isset($user['role_id']) && $user['role_id'] == 1;

// Récupérer tous les rôles depuis la base
$roles = [];
$stmt = $db->query("SELECT * FROM roles");
$allRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($allRoles as $r) {
    $role = new Role($r['id'], $r['nom'], $r['description']);
    $roles[] = $role;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($readonly) {
        die("Impossible de modifier les données d'un admin !");
    }

    $id = intval($_POST['id']);
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $planet = trim($_POST['planet']);
    $statut = $_POST['statut'] ?? $user['statut'];
    $role_id = intval($_POST['role_id'] ?? $user['role_id']);
    $newPassword = trim($_POST['password'] ?? '');

    // Vérifier que la planète est valide
    if (!in_array($planet, $planets)) {
        die("Planète invalide.");
    }

    // Vérifier que le rôle sélectionné existe
    $roleValid = false;
    foreach ($roles as $r) {
        if ($r->getId() === $role_id) {
            $roleValid = true;
            break;
        }
    }
    if (!$roleValid) {
        die("Rôle invalide.");
    }

    $passwordSql = '';
    $params = [];

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $passwordSql = ', password=?';
        $params[] = $hashedPassword;
    }

    $stmt = $db->prepare("UPDATE user SET pseudo=?, email=?, statut=?, role_id=?, planet=? $passwordSql WHERE id=?");
    $params = array_merge([$pseudo, $email, $statut, $role_id, $planet], $params, [$id]);
    $stmt->execute($params);

    header("Location: ../../../view/Admin/login/src/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="../../../view/Client/login/assets/css/edit_user.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Modifier l'utilisateur</h1>

    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

        <label>Pseudo :</label>
        <input type="text" name="pseudo" value="<?= htmlspecialchars($user['pseudo'] ?? '') ?>" <?= $readonly ? 'readonly' : '' ?>>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" <?= $readonly ? 'readonly' : '' ?>>

        <label>Planète :</label>
        <select name="planet" <?= $readonly ? 'disabled' : '' ?>>
            <?php foreach ($planets as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>" <?= (isset($user['planet']) && $user['planet'] == $p) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Nouveau mot de passe :</label>
        <input type="password" name="password" placeholder="••••••" <?= $readonly ? 'disabled title="Impossible de modifier un admin"' : '' ?>>

        <?php if (!$readonly): ?>
            <label>Statut :</label>
            <select name="statut">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>" <?= $user['statut'] == $status ? 'selected' : '' ?>>
                        <?= htmlspecialchars($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Rôle :</label>
            <select name="role_id">
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r->getId() ?>" <?= $user['role_id'] == $r->getId() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r->getNom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p>Statut : <?= htmlspecialchars($user['statut']) ?> (Admin)</p>
            <p>Rôle : Admin</p>
        <?php endif; ?>

        <button type="submit" class="btn-register" <?= $readonly ? 'disabled title="Impossible de modifier un admin"' : '' ?>>💾 Enregistrer</button>
        <a href="../../../view/Admin/login/src/dashboard.php" class="btn-logout">Annuler</a>
    </form>
</div>
</body>
</html>
