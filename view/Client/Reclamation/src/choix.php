<?php
include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../model/Reclamtion/reclam.php';

$error = "";
$success = "";
$avatar = $_SESSION['avatar'] ?? '';

// Si l'avatar vient de la base avec ../ on nettoie
$avatar = ltrim($avatar, './');

// Chemin réel serveur
$fullPath = $_SERVER['DOCUMENT_ROOT'] . '/projet-web/' . $avatar;

// Si le fichier n'existe pas → image par défaut
if (empty($avatar) || !file_exists($fullPath)) {
    $avatar = 'view/Client/login/uploads/avatars/default.png';
}
if($_POST) {
    $user = "Utilisateur Anonyme";
    $sujet = $_POST['sujet_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';

    // Validation
    $errors = validateReclamation($user, $sujet, $description);

    if(empty($errors)) {
        $controller = new ReclamationController();
        if($controller->addReclamation($user, $sujet, $description, $statut)) {
            $success = "Réclamation ajoutée avec succès!";
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
    <title>Nouvelle Réclamation</title>

    <!-- CSS du header -->
    <link rel="stylesheet" href="../../assets/css/user.css">

    <!-- CSS du formulaire choix -->
    <link rel="stylesheet" href="../assets/css/choix.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>

<body class="dark">

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation"></div>

<header>
    <div class="container">
        <h1>Tunispace</h1>
        <nav>
            <a href="/projet-web/view/Client/index.php">Home</a>
            <a href="/projet-web/view/Client/Innovation/src/categories.php">Categories</a>
            <a href="#messages">Messages</a>
            <a href="/projet-web/view/Client/Reclamation/src/choix.php" class="active">Reclamation</a>
        </nav>
        <div style="position:relative">
            <img
                    id="user-avatar"
                    src="/projet-web/<?= $avatar ?>"
                    alt="Avatar utilisateur"
            >


            <div id="user-dropdown">
                <div class="dropdown-item" id="myProfileBtn">
                    <i class="fas fa-user"></i> My Profile
                </div>
                <div class="dropdown-item"><i class="fas fa-moon"></i> Dark Mode
                    <label class="ml-auto"><input type="checkbox" id="theme-switch" class="toggle-checkbox" checked><span class="toggle-label"></span></label>
                </div>
                <div class="dropdown-item"><i class="fas fa-bell"></i> Notifications</div>
                <div class="dropdown-item"><i class="fas fa-cog"></i> Settings</div>
                <hr class="border-gray-700 my-2">
                <div class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Logout</div>
            </div>
        </div>
    </div>
</header>




<main class="choix-wrapper">
    <div class="text-center">
        <h2>Nouvelle réclamation</h2>
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
                <label>Problème technique</label>
                <img src="../assets/img/PT.jpg" alt="Problème technique">
            </div>
            <div class="img-box" data-type="probleme de securite">
                <label>Problème de sécurité</label>
                <img src="../assets/img/PS.jpg" alt="Problème sécurité">
            </div>
            <div class="img-box" data-type="Compte bloqué">
                <label>Compte bloqué</label>
                <img src="../assets/img/CB.jpg" alt="Compte bloqué">
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
                    <span class="custom-option" data-value="fr">Français</span>
                    <span class="custom-option" data-value="en">Anglais</span>
                    <span class="custom-option" data-value="ar">Arabe</span>
                </div>
            </div>

            <!-- champ réel pour POST -->
            <input type="hidden" name="language" id="language">


            <input type="hidden" name="statut" value="en attente">

            <button type="submit" class="btn-submit">Suivant</button>
        </form>
    </section>
</main>

<footer>
    <p>© 2025 — Tunispace Galaxy</p>
</footer>

<!-- JS -->
<script src="../../assets/js/user.js"></script>
<script src="../assets/js/choix.js"></script>

</body>
</html>
