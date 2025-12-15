<?php
session_start();
include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../model/Reclamtion/reclam.php';

// Vérifier que l'utilisateur est connecté
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../../LoginC/login.html");
    exit;
}

// Connexion à la base
$db = config::getConnexion();

// Récupérer les infos utilisateur
$stmt = $db->prepare("SELECT pseudo, avatar FROM user WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Définir le pseudo et avatar
$username = $user['pseudo'] ?? "Utilisateur";
$avatarFromDB = $user['avatar'] ?? '';
$avatarDir = '/projet-web/view/Client/uploads/avatars/';
$defaultAvatar = $avatarDir . 'default.jpg';
$avatarPathServer = $_SERVER['DOCUMENT_ROOT'] . $avatarDir . ltrim($avatarFromDB, '/');

// Vérifier si l’avatar existe sinon mettre par défaut
$avatarUrl = (!empty($avatarFromDB) && file_exists($avatarPathServer))
        ? $avatarDir . ltrim($avatarFromDB, '/')
        : $defaultAvatar;

// Gestion du formulaire
$error = "";
$success = "";

if ($_POST) {
    $sujet = $_POST['sujet_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';

    // Validation
    $errors = validateReclamation($username, $sujet, $description);

    if (empty($errors)) {
        $controller = new ReclamationController();
        if ($controller->addReclamation($username, $sujet, $description, $statut)) {
            $success = "Réclamation ajoutée avec succès !";
            $_POST = array();
        } else {
            $error = "Erreur lors de l'ajout de la réclamation";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nouvelle RÃ©clamation</title>

    <!-- CSS du header -->
    <link rel="stylesheet" href="../../assets/css/user.css">

    <!-- CSS du formulaire choix -->
    <link rel="stylesheet" href="../assets/css/choix.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>

<body class="dark">

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <div class="container">
        <h1>TuniSpace</h1>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="../../Innovation/src/categories.php" ">Categorie</a>
            <a href="#messages">Messages</a>
            <a href="#" class="active">Reclamation </a>
        </nav>

        <div style="position:relative">

            <img id="user-avatar"
                 src="<?= htmlspecialchars($avatarUrl) ?>"
                 alt="<?= htmlspecialchars($username) ?>"
                 style="cursor:pointer;">

            <div id="user-dropdown">

                <div class="dropdown-item"
                     onclick="window.location.href='../../profile.php';"
                     style="cursor:pointer;">
                    <?= htmlspecialchars($username) ?>
                </div>

                <div class="dropdown-item">
                    Dark Mode
                    <label class="ml-auto">
                        <input type="checkbox" id="theme-switch" class="toggle-checkbox" checked>
                        <span class="toggle-label"></span>
                    </label>
                </div>

                <div class="dropdown-item">Notifications</div>
                <div class="dropdown-item">Settings</div>

                <hr class="border-gray-700 my-2">

                <div class="dropdown-item logout">
                    <a href="/projet-web/logout.php"
                       style="color:#ff002d;text-decoration:none;">
                        get out
                    </a>
                </div>

            </div>
        </div>

    </div>
</header>




<main class="choix-wrapper">
    <div class="text-center">
        <h2>Nouvelle reclamation</h2>
    </div>

    <?php if($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- FORMULAIRE -->
    <section class="form-container">

        <!-- IMAGES CHOIX -->
        <div class="image-grid">
            <div class="img-box" data-type="Contenu incorrect">
                <label>Contenu incorrect</label>
                <img src="../assets/img/CI.jpg" alt="Contenu incorrect">
            </div>
            <div class="img-box" data-type="Probleme technique">
                <label>Probleme technique</label>
                <img src="../assets/img/PT.jpg" alt="Probleme technique">
            </div>
            <div class="img-box" data-type="probleme de securite">
                <label>Probleme de securite</label>
                <img src="../assets/img/PS.jpg" alt="Probleme sÃ©curite">
            </div>
            <div class="img-box" data-type="Compte bloque">
                <label>Compte bloque</label>
                <img src="../assets/img/CB.jpg" alt="Compte bloque">
            </div>
        </div>

        <!-- FORMULAIRE -->
        <form id="contactForm" method="POST" action="">
            <input type="hidden" id="sujet_type" name="sujet_type" value="">

            <label for="language">Choisir la langue</label>
            <div class="custom-select">
                <div class="custom-select-trigger">
                    <span id="selected-option">Choisir la langue</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>

                <div class="custom-options">
                    <span class="custom-option" data-value="fr">Francais</span>
                    <span class="custom-option" data-value="en">Anglais</span>
                    <span class="custom-option" data-value="ar">Arabe</span>
                </div>
            </div>


			<!-- captcha-->
			<!-- Ajoutez ceci APRÈS le champ hidden "language" dans votre formulaire -->
<input type="hidden" name="language" id="language">

<!-- AJOUT DU CAPTCHA ICI -->
<div class="captcha-container">
    <label for="captcha">Vérification de sécurité</label>
    <div class="captcha-box">
        <div class="captcha-code" id="captchaText"></div>
        <button type="button" class="captcha-refresh" id="refreshCaptcha">
            <i class="fas fa-redo"></i>
        </button>
    </div>
    <input type="text" id="captcha" name="captcha" placeholder="Entrez le code ci-dessus" required>
    <div class="captcha-error" id="captchaError"></div>
</div>


<!-- ------------- -->

            <!-- champ rÃ©el pour POST -->
            <input type="hidden" name="language" id="language">


            <input type="hidden" name="statut" value="en attente">

            <button type="submit" class="btn-submit">Suivant</button>
        </form>
    </section>
</main>

<footer>
    <p>© 2025 ” Tunispace Galaxy</p>
</footer>

<!-- JS -->
<script src="../../assets/js/user.js"></script>
<script src="../assets/js/choix.js"></script>

</body>
</html>
