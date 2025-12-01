<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireLogin();
?>

<?php
require_once __DIR__ . "/../../../../controller/components/Innovation/inns_Config.php";
$innCtrl = new InnovationController();
$catCtrl = new CategoryController();

$userMode = isset($_GET['user']) || isset($_GET['userid']);
     // ?user  → Mes innovations
$catMode  = isset($_GET['categorie']);    // ?categorie=ID → Innovations validées d'une catégorie

// Mode 1 : MES INNOVATIONS
if ($userMode) {
    $userId = isset($_GET['user']) ? intval($_GET['user']) : intval($_GET['userid']);
    $innovations = $innCtrl->listInnovationsByUser($userId);
}

 elseif ($catMode) {
    $catId = intval($_GET['categorie']);

    $innovations = $innCtrl->listInnovationsByCategory($catId);

    // On garde SEULEMENT les innovations validées
    $innovations = array_filter($innovations, fn($i) => $i['statut'] === 'Validée');

// Mode 3 : AFFICHER TOUTES LES VALIDÉES
} else {
    $innovations = $innCtrl->listInnovations();
    $innovations = array_filter($innovations, fn($i) => $i['statut'] === 'Validée');
}

$msg = $_GET['msg'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Innovations </title>

    <!-- Future Galaxy Web3 CSS -->
    <link rel="stylesheet" href="../assets/css/list_innovation.css">
</head>

<body>

<header class="cs-header">
    <div>
        <h1>
            <?php if ($userMode): ?>
                Vos innovations
            <?php elseif ($catMode): ?>
                Innovations validées de la catégorie
            <?php else: ?>
                Toutes les innovations validées
            <?php endif; ?>
        </h1>

        <p style="opacity:0.8;margin:5px 0 0;">
            <?php if ($userMode): ?>
                Retrouvez ici toutes vos innovations : validées, refusées ou en attente.
            <?php elseif ($catMode): ?>
                Affichage des innovations approuvées uniquement.
            <?php else: ?>
                Liste globale des innovations confirmées.
            <?php endif; ?>
        </p>
    </div>
    <div class="cs-container">
        <nav class="cs-nav">
            <a href="../../index.php">Accueil</a>
            <a href="categories.php">Catégories</a>
            <a href="add_Innovation.php">Ajouter une Innovation</a>
            <a href="list_Innovation.php?user=<?= urlencode($_SESSION['user_id'] ?? 0) ?>">
                Mes innovations
            </a>
        </nav>
    </div>
</header>

<main>
    <?php if ($catMode): ?>
        <a href="categories.php" class="btn-return">
            ← Retour aux catégories
        </a>
    <?php endif; ?>

    <?php if ($msg === 'added'): ?>
        <p class="msg-success">✅ Votre innovation a été soumise.</p>
    <?php endif; ?>
    <?php if ($msg === 'updated'): ?>
        <p class="msg-success">
            ✅ Innovation modifiée et renvoyée pour validation.
        </p>
    <?php endif; ?>


    <div class="cards-grid">
        <?php foreach ($innovations as $inn): ?>
            <article class="innovation-card">

                <header class="card-header">
                    <div class="card-title-id">
                        <h2 class="card-title"><?= htmlspecialchars($inn['titre']) ?></h2>
                    </div>
                    <span class="card-category">
                    <?= htmlspecialchars($inn['categorie_nom']) ?>
                </span>
                </header>

                <div class="card-body">
                    <div class="card-row">
                        <span class="card-label">Date de création</span>
                        <span class="card-value"><?= htmlspecialchars($inn['date_creation']) ?></span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Statut</span>
                        <span class="card-value">
        <?php
        $statut = $inn['statut'] ?? 'Inconnu';

        // Classe CSS en fonction du statut
        $badgeClass = 'badge-refused';
        if ($statut === 'Validée') {
            $badgeClass = 'badge-valid';
        } elseif ($statut === 'Refusée') {
            $badgeClass = 'badge-refused';
        } elseif ($statut === 'En attente') {
            $badgeClass = 'badge-pending';
        }
        ?>
        <span class="badge <?= $badgeClass ?>">
            <?= htmlspecialchars($statut) ?>
        </span>
    </span>
                    </div>

                </div>

                <footer class="card-footer">

                    <?php if ($userMode): ?>
                        <a href="edit_Innovation.php?id=<?= $inn['id'] ?>" class="btn-primary">
                            Modifier
                        </a>
                    <?php endif; ?>

                    <a class="btn-primary"
                       href="details_Innovation.php?id=<?= urlencode($inn['id']) ?>">
                        Voir
                    </a>

                </footer>


            </article>
        <?php endforeach; ?>
    </div>


</main>

<footer>
    <p>&copy; 2025 - Innovation - Tunispace - Hichem Challakhi</p>
</footer>

<!-- Future Galaxy Web3 JS -->
<script src="../assets/js/list_innovation.js"></script>

</body>
</html>
