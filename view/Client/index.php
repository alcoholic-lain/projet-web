<?php
session_start();
require_once '../../config.php';
require_once '../../protect.php'; // Vérifie que l'utilisateur est connecté

$db = config::getConnexion();

// Récupérer toutes les infos de l'utilisateur connecté
$stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe pas, redirection vers login
if (!$user) {
    header('Location: ../LoginC/login.html');
    exit;
}

// Gestion de l'avatar
$defaultAvatar = 'view/Client/uploads/avatars/default.jpg'; // URL publique par défaut

// Si avatar vide ou null → utiliser avatar par défaut
$avatar = !empty($user['avatar']) ? ltrim($user['avatar'], './') : $defaultAvatar;

// Chemin complet sur le serveur pour vérifier existence
$fullPath = $_SERVER['DOCUMENT_ROOT'] . '/projet-web/' . $avatar;
if (!file_exists($fullPath)) {
    $avatar = $defaultAvatar;
}

// URL publique pour <img src="">
$avatarUrl = '/projet-web/' . $avatar;
$eventStmt = $db->query("
    SELECT e.*, i.titre 
    FROM innovation_events e
    JOIN innovations i ON i.id = e.innovation_id
    WHERE e.event_date > NOW()
    ORDER BY e.event_date ASC
    LIMIT 1
");
$nextEvent = $eventStmt->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Space - Cosmic Messenger</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/user.css">
</head>

<body class="dark">

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <div class="container">
        <h1>Tunispace</h1>
        <nav>
            <a href="#home" class="active">Home</a>
            <a href="Innovation/src/categories.php">Categories</a>
            <a href="#messages">Messages</a>
            <a href="Reclamation/src/choix.php">Reclamation</a>
        </nav>

        <div style="position:relative">
            <!-- Avatar utilisateur -->
            <img id="user-avatar" src="<?= htmlspecialchars($avatarUrl) ?>" alt="<?= htmlspecialchars($user['pseudo']) ?>" style="cursor:pointer;">

            <div id="user-dropdown">
                <div class="dropdown-item" onclick="window.location.href='profile.php';">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user['pseudo']) ?>
                </div>

                <div class="dropdown-item">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                </div>

                <div class="dropdown-item">
                    <i class="fas fa-moon"></i> Dark Mode
                    <label class="ml-auto">
                        <input type="checkbox" id="theme-switch" class="toggle-checkbox" checked>
                        <span class="toggle-label"></span>
                    </label>
                </div>

                <div class="dropdown-item"><i class="fas fa-bell"></i> Notifications</div>
                <div class="dropdown-item"><i class="fas fa-cog"></i> Settings</div>

                <hr class="border-gray-700 my-2">

                <div class="dropdown-item logout" onclick="window.location.href='../../logout.php';">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </div>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="home-grid">

        <!-- GAUCHE : STORY -->
        <div class="content-card left-aligned">
            <h2>Every idea has a moment.</h2>
            <p>
                Some ideas are born quietly.<br>
                Some disappear.<br>
                Others change everything.
            </p>

            <div class="timeline-item now">
                <h3>🕒 Now</h3>
                <p><strong><?= htmlspecialchars($user['pseudo']) ?></strong>, ideas are forming right now on Tunispace.</p>
            </div>
        </div>

        <!-- DROITE : EVENTS NASA -->
        <div class="events-panel">
            <div class="event moon">

            🌕 <b>Total Lunar Eclipse</b>
                <span class="countdown" data-date="2026-03-03T08:44:25Z"></span>
                <small>— Moon</small>
            </div>

            <div class="event sun">
                ☀️ <b>Total Solar Eclipse</b>
                <span class="countdown" data-date="2026-08-12T16:00:00Z"></span>
                <small>— Sun</small>
            </div>

            <div class="event mars">
                🔴 <b>Mars Opposition</b>
                <span class="countdown" data-date="2027-02-19T00:00:00Z"></span>
                <small>— Mars</small>
            </div>

            <div class="event earth">
                🌍 <b>Earth Perihelion</b>
                <span class="countdown" data-date="2026-01-04T00:00:00Z"></span>
                <small>— Earth</small>
            </div>

            <div class="event highlight unknown">
                <div class="timeline-item future unknown">

                🌟 <b>Something will explode</b>
                <span class="countdown" data-date="2025-12-31T23:59:59Z"></span>
                    <p style="opacity:.7;font-size:.85rem">
                        Maybe a star. Maybe an idea born here.
                    </p>

                </div>

        </div>

    </div>

</main>

<div id="dm-btn"><i class="fas fa-comment-dots"></i></div>

<div id="dm-popup">
    <div class="chat-header">
        <div class="chat-header-left">
            <button id="back-to-list" style="display:none;background:none;border:none;color:white;font-size:18px;cursor:pointer;opacity:0.9">
                <i class="fas fa-arrow-left"></i>
            </button>
            <img id="popup-avatar" src="" alt="" style="opacity:0">
            <div class="chat-info">
                <h3 id="popup-name">Messages</h3>
                <p id="popup-status"></p>
            </div>
        </div>

        <div class="header-buttons">
            <button id="minimize-btn"><i class="fas fa-minus"></i></button>
            <button id="maximize-btn"><i class="fas fa-expand"></i></button>
            <button id="close-popup"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="popup-body">
        <div class="conversations-panel" id="conversations-panel">
            <div class="search-section">
                <input type="text" id="search-input" placeholder="Search contacts...">
            </div>

            <div class="contacts-list">
                <div class="contact-item" data-contact="sarah">
                    <img src="https://randomuser.me/api/portraits/women/32.jpg">
                    <div class="info">
                        <div class="name">Sarah Johnson</div>
                        <div class="preview">Sounds good!</div>
                    </div>
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                </div>
                <div class="contact-item" data-contact="mike">
                    <img src="https://randomuser.me/api/portraits/men/45.jpg">
                    <div class="info">
                        <div class="name">Mike Chen</div>
                        <div class="preview">Meeting at 3 PM</div>
                    </div>
                </div>
                <div class="contact-item" data-contact="emma">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg">
                    <div class="info">
                        <div class="name">Emma Davis</div>
                        <div class="preview">Thanks!</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-panel hidden" id="chat-panel">
            <div id="popup-messages"></div>
            <div class="input-bar">
                <button><i class="fas fa-paperclip"></i></button>
                <input type="text" id="popup-input" placeholder="Type a message...">
                <button id="popup-send"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>Crafted with <span>♥</span> in the cosmos — <span>Space</span> © 2025</p>
</footer>

<script src="assets/js/user.js"></script>
</body>
</html>
