<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";

$innCtrl = new InnovationController();
$catCtrl = new CategoryController();

$userMode = isset($_GET['user']);         // ?user=1  → Mes innovations
$catMode  = isset($_GET['categorie']);    // ?categorie=ID → Innovations validées d'une catégorie

// Mode 1 : MES INNOVATIONS
if ($userMode) {
    // !!! Remplacer 1 par $_SESSION['user_id'] lorsque login sera actif
    $userId = 1;

    $innovations = $innCtrl->listInnovationsByUser($userId);

// Mode 2 : AFFICHER PAR CATÉGORIE
} elseif ($catMode) {
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
    <title>Liste des Innovations – Innovation</title>

    <!-- Future Galaxy Web3 CSS -->
    <link rel="stylesheet" href="../assets/css/list_innovation.css">
</head>

<body>

<header>
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

    <nav>
        <a href="../../index.php">🏠 Accueil</a>
        <a href="categories.php" class="cs-nav-active">Catégories</a>
        <a href="add_Innovation.php">➕ Ajouter une innovation</a>
        <a href="list_Innovation.php?user=1">📁 Mes innovations</a>
    </nav>
</header>

<main>

    <?php if ($msg === 'added'): ?>
        <p class="msg-success">✅ Votre innovation a été soumise.</p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Détails</th>
        </tr>

        <?php if (empty($innovations)): ?>
            <tr>
                <td colspan="6">Aucune innovation trouvée.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($innovations as $inn): ?>
                <tr>
                    <td><?= htmlspecialchars($inn['id']) ?></td>

                    <td><?= htmlspecialchars($inn['titre']) ?></td>

                    <td><?= htmlspecialchars($inn['categorie_nom'] ?? '—') ?></td>

                    <td><?= htmlspecialchars($inn['date_creation']) ?></td>

                    <td>
                        <?php if ($inn['statut'] === "Validée"): ?>
                            <span class="badge badge-valid">Validée</span>
                        <?php elseif ($inn['statut'] === "En attente"): ?>
                            <span class="badge badge-pending">En attente</span>
                        <?php else: ?>
                            <span class="badge badge-refused">Refusée</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a class="btn-primary"
                           href="details_Innovation.php?id=<?= urlencode($inn['id']) ?>">
                            Voir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

</main>

<footer>
    <p>&copy; 2025 - Innovation - Tunispace - Hichem Challakhi</p>
</footer>

<!-- Future Galaxy Web3 JS -->
<script src="../assets/js/list_innovation.js"></script>

</body>
</html>
