<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header('Location: ../view/FrontOffice/login.html');
    exit;
}

require_once '../config.php';
$db = Config::getConnexion();

// Listes autorisées
$planets = ['Mars', 'Terre', 'Jupiter', 'Saturne', 'Venus'];
$statuses = ['Actif', 'Banni', 'Inactif'];

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        die("Utilisateur introuvable !");
    }
}

// Traitement du formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['id'];
    $pseudo = $_POST['pseudo'];
    $email = $_POST['email'];
    $planet = $_POST['planet'];

    // Vérification que la planète est valide
    if(!in_array($planet, $planets)){
        die("Planète invalide.");
    }

    // Si l'utilisateur n'est pas admin, on peut modifier le rôle et le statut
    if($user['role_id'] != 1){
        $statut = $_POST['statut'];
        $role_id = $_POST['role_id'];

        if(!in_array($statut, $statuses)){
            die("Statut invalide.");
        }
        if($role_id != 1 && $role_id != 2){
            die("Rôle invalide.");
        }

        $stmt = $db->prepare("UPDATE user SET pseudo=?, email=?, statut=?, role_id=?, planet=? WHERE id=?");
        $stmt->execute([$pseudo, $email, $statut, $role_id, $planet, $id]);
    } else {
        // Pour un admin, on ne touche pas au rôle ni au statut
        $stmt = $db->prepare("UPDATE user SET pseudo=?, email=?, planet=? WHERE id=?");
        $stmt->execute([$pseudo, $email, $planet, $id]);
    }

    header("Location: ../view/BackEnd/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="../view/FrontOffice/assets/css/dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Modifier l'utilisateur</h1>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <label>Pseudo :</label>
        <input type="text" name="pseudo" value="<?= htmlspecialchars($user['pseudo']) ?>" required>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Planète :</label>
        <select name="planet" required>
            <?php foreach($planets as $p): ?>
                <option value="<?= $p ?>" <?= $user['planet']==$p?'selected':'' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select>

        <?php if($user['role_id'] != 1): ?>
            <label>Statut :</label>
            <select name="statut" required>
                <?php foreach($statuses as $status): ?>
                    <option value="<?= $status ?>" <?= $user['statut']==$status?'selected':'' ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>

            <label>Rôle :</label>
            <select name="role_id" required>
                <option value="1" <?= $user['role_id']==1?'selected':'' ?>>Admin</option>
                <option value="2" <?= $user['role_id']==2?'selected':'' ?>>User</option>
            </select>
        <?php else: ?>
            <p>Statut : <?= $user['statut'] ?> (Admin)</p>
            <p>Rôle : Admin</p>
        <?php endif; ?>

        <button type="submit" class="btn-register">💾 Enregistrer</button>
        <a href="../view/BackEnd/dashboard.php" class="btn-logout">Annuler</a>
    </form>
</div>
</body>
</html>
