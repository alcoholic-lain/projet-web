<?php
session_start();
require_once '../../config.php';
require_once '../../protect.php';

$db = config::getConnexion();

// Récupération infos utilisateur
$stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

// Avatar et statut
$avatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/img/default-avatar.png';
$statusColor = ($user['statut'] === 'actif') ? '#4CAF50' : '#888';
$statusText  = ($user['statut'] === 'actif') ? 'Actif' : 'Inactif';


// ===================================================================
//  BADGES AUTOMATIQUES SELON NOMBRE DE CONNEXIONS
// ===================================================================

// Fonction pour ajouter un badge (déjà existante)
function addBadge($userId, $badgeId, $db) {
    $stmt = $db->prepare("SELECT * FROM user_badges WHERE user_id = ? AND badge_id = ?");
    $stmt->execute([$userId, $badgeId]);

    if ($stmt->rowCount() == 0) {
        $stmtInsert = $db->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmtInsert->execute([$userId, $badgeId]);
    }
}

// Récupérer nombre de connexions depuis user_activity
$stmtConn = $db->prepare("SELECT COUNT(*) FROM user_activity WHERE user_id = ? AND action = 'connexion'");
$stmtConn->execute([$_SESSION['user_id']]);
$loginCount = $stmtConn->fetchColumn();


// Attribuer les badges selon le nombre de connexions
if ($loginCount >= 1)   addBadge($_SESSION['user_id'], 1, $db);   // Nouveau
if ($loginCount >= 10)  addBadge($_SESSION['user_id'], 2, $db);   // Actif
if ($loginCount >= 50)  addBadge($_SESSION['user_id'], 3, $db);   // Fidèle
if ($loginCount >= 100) addBadge($_SESSION['user_id'], 4, $db);   // Loyal
if ($loginCount >= 500) addBadge($_SESSION['user_id'], 5, $db);   // Légende


// ===================================================================
//  Récupération des badges de l’utilisateur
// ===================================================================
$stmtBadges = $db->prepare("
    SELECT b.id, b.name, b.description, b.icon
    FROM user_badges ub
    JOIN badges b ON ub.badge_id = b.id
    WHERE ub.user_id = ?
");
$stmtBadges->execute([$_SESSION['user_id']]);
$userBadges = $stmtBadges->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil • Space</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../LoginC/assets/css/profile.css">
    <style>
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .badge {
            background: #14173d;
            border-radius: 12px;
            padding: 20px;
            width: 130px;
            text-align: center;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            transition: 0.3s;
        }
        .badge:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255,255,255,0.4);
        }
        .badge img {
            display: block;
            margin: 0 auto;
            width: 48px;
            height: 48px;
        }
        .badge-name {
            font-size: 18px;
            margin-top: 10px;
            color: #fff;
        }
    </style>
</head>
<body>

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <div class="container" style="max-width:1400px;margin:auto;padding:0 32px;display:flex;justify-content:space-between;align-items:center;height:70px;">
        <h1 style="font-size:2rem;font-weight:800;background:linear-gradient(90deg,#ff7f2a,#ff2a7f);-webkit-background-clip:text;color:transparent;">TuniSpace</h1>

        <!-- NAVBAR -->
        <nav style="display:flex;gap:30px;align-items:center;">
            <a href="F_index.php" class="nav-link active">Home</a>
            <a href="Innovation/src/categories.php" class="nav-link">Categories</a>
            <a href="#" class="nav-link">Messages</a>
        </nav>

        <!-- AVATAR -->
        <div style="position:relative;">
            <img id="user-avatar" src="<?= htmlspecialchars($avatar) ?>" alt="You" style="
                width:42px;
                height:42px;
                border-radius:50%;
                object-fit:cover;
                border:2.5px solid rgba(255,127,42,.6);
                box-shadow:0 0 20px rgba(255,127,42,.4);
                cursor:pointer;
                transition: transform 0.25s;
            ">

            <!-- DROPDOWN -->
            <div id="user-dropdown" style="
                position:absolute;
                top:60px;
                right:0;
                width:220px;
                background:rgba(20,25,50,0.95);
                border:1px solid rgba(255,255,255,.15);
                border-radius:12px;
                padding:8px 0;
                box-shadow:0 10px 30px rgba(0,0,0,.6);
                opacity:0;
                visibility:hidden;
                transition: all 0.3s;
                z-index:1000;
            ">
                <div class="dropdown-item" onclick="location.href='profile.php'" style="padding:10px 20px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-user"></i> My Profile
                </div>
                <div class="dropdown-item logout" onclick="location.href='../../logout.php'" style="padding:10px 20px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </div>
            </div>
        </div>
    </div>

    <!-- STYLES NAVBAR -->
    <style>
        .nav-link {
            font-weight:600;
            color:#fff;
            text-decoration:none;
            transition: all 0.25s;
        }
        .nav-link:hover {
            color:#ff7f2a;
        }
        .nav-link.active {
            color:#ff7f2a;
            font-weight:700;
        }

        /* Dropdown toggle */
        #user-avatar:hover + #user-dropdown,
        #user-dropdown:hover {
            opacity:1;
            visibility:visible;
        }

        .dropdown-item:hover {
            background:rgba(255,127,42,.1);
        }
    </style>
</header>


<main>
    <div class="profile-wrapper">
        <div class="profile-card">
            <h1>Mon Profil</h1>

            <!-- Avatar -->
            <div class="avatar-section" style="text-align:center;">
                <div style="position:relative; display:inline-block;">
                    <img src="<?= htmlspecialchars($avatar) ?>" id="avatarPreview" alt="Avatar" style="width:100px;height:100px;border-radius:50%;">
                    <span id="statusBadge" style="
                            position:absolute;
                            bottom:8px;
                            right:8px;
                            width:16px;
                            height:16px;
                            background:<?= $statusColor ?>;
                            border:2px solid #fff;
                            border-radius:50%;
                            " title="<?= $statusText ?>"></span>
                </div>

                <form id="avatarForm" enctype="multipart/form-data" style="margin-top:10px;">
                    <label class="upload-btn">Changer l’avatar
                        <input type="file" name="avatar" accept="image/*" hidden>
                    </label>
                    <button class="btn-save" type="submit">Enregistrer</button>
                </form>
            </div>

            <!-- Statut -->
            <div class="status-section" style="text-align:center; margin-top:20px;">
                <p id="statusText" style="font-weight:600; color:#fff;"><?= $statusText ?></p>
                <form id="statusForm">
                    <label for="statusSelect">Modifier votre statut :</label>
                    <select id="statusSelect" name="status">
                        <option value="actif" <?= $user['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= $user['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                    <button type="submit" class="btn-save">Modifier</button>
                </form>
            </div>

            <!-- Pseudo + email -->
            <form id="profileForm" style="margin-top:30px;">
                <label>Pseudo</label>
                <input type="text" name="pseudo" value="<?= htmlspecialchars($user['pseudo']) ?>">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                <button class="btn-save" type="submit">Enregistrer les modifications</button>
            </form>

            <!-- Badges -->
            <h2 style="margin-top:30px;">Mes Badges</h2>
            <div class="badge-container">
                <?php foreach($userBadges as $badge): ?>
                    <div class="badge">
                        <img src="<?= '../../icons/' . htmlspecialchars($badge['icon']) ?>" alt="<?= htmlspecialchars($badge['name']) ?>">
                        <div class="badge-name"><?= htmlspecialchars($badge['name']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Mot de passe -->
            <div style="margin-top:40px;">
                <h2>Changer le mot de passe</h2>
                <form id="passwordForm">
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="Mot de passe actuel">
                    <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer">
                    <button class="btn-save" type="submit">Modifier le mot de passe</button>
                </form>
            </div>

            <br><br>
            <button id="downloadData" class="btn-save">Télécharger mes données</button>
            <button class="btn-save btn-logout" onclick="window.location.href='../../logout.php'">Se déconnecter</button>

        </div>
    </div>
</main>

<script src="../LoginC/assets/js/profile.js" defer></script>
</body>
</html>
