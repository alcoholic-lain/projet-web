<?php
require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$ctrl = new InnovationController();
$error = null;
// ==== VARIABLES POUR HEADER + SIDEBAR ====
$pageTitle     = "🚀 Gestion des Innovations";
$pageSubtitle  = "Administration des projets d’innovation";

$activeMenu = 'innovations';
$activeSub  = isset($_GET['pending']) ? 'innovations_pending' : 'innovations_all';

// Gestion suppression
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        try {
            $ctrl->deleteInnovation($id);
            header("Location: a_Innovation.php?msg=deleted");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Récupération des innovations
// Récupération des innovations
try {
    $innovations = $ctrl->listInnovations();

    // FILTRE : si ?pending est dans l'URL → ne garder que les innovations en attente
    if (isset($_GET['pending'])) {
        $innovations = array_filter($innovations, function($i) {
            return $i['statut'] === 'En attente';
        });

        // Active la sous-catégorie dans le sidebar
        $activeSub = 'innovations_pending';
    }

} catch (Exception $e) {
    $innovations = [];
    $error = $e->getMessage();
}

$msg = $_GET['msg'] ?? null;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <!-- CSS GLOBAL ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <!-- CSS SPÉCIFIQUE À CETTE PAGE -->
    <link rel="stylesheet" href="../assets/css/a_Innovation.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR GLOBAL -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER GLOBAL -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<!-- MAIN -->
<main>
    <div class="dashboard-inner">

        <div class="page-header-row">
            <h2 class="section-title-main">Liste des innovations</h2>
            <a href="add_Innovation.php" class="btn-add">➕ Ajouter une innovation</a>
        </div>

        <?php if ($msg === 'deleted'): ?>
            <p class="success">✅ Innovation supprimée avec succès.</p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error">❌ <?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <section class="section-box">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php if (!empty($innovations)): ?>
                    <?php foreach ($innovations as $inn): ?>
                        <tr>
                            <td><?= htmlspecialchars($inn['id']) ?></td>
                            <td><?= htmlspecialchars($inn['titre']) ?></td>
                            <td><?= htmlspecialchars($inn['description']) ?></td>
                            <td><?= htmlspecialchars($inn['categorie_nom']) ?></td>
                            <td><?= htmlspecialchars($inn['statut']) ?></td>
                            <td><?= htmlspecialchars($inn['date_creation']) ?></td>
                            <td class="actions-cell">

                                <!-- Bouton Modifier -->
                                <a href="edit_Innovation.php?id=<?= urlencode($inn['id']) ?>"
                                   class="btn-icon edit" title="Modifier">
                                    ✏️
                                </a>

                                <!-- Bouton Supprimer -->
                                <a href="a_Innovation.php?delete=<?= urlencode($inn['id']) ?>"
                                   class="btn-icon delete"
                                   title="Supprimer"
                                   onclick="return confirm('Supprimer cette innovation ?');">
                                    🗑️
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">Aucune innovation trouvée.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>
<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>
<!-- JS SPÉCIFIQUE À CETTE PAGE -->
<script src="../assets/js/a_Innovation.js"></script>

</body>
</html>
