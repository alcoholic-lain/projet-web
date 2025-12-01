<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireAdmin();
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/projet-web/config.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../../Client/login/login.html');
    exit;
}

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
            <a href="add_Innovation.php" class="btn-add"> Ajouter une innovation</a>
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
                    <th>Utilisateur</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Fichier</th>
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

                            <!-- ✅ ID -->
                            <td><?= htmlspecialchars($inn['utilisateur'] ?? '—') ?></td>

                            <!-- ✅ Titre -->
                            <td><?= htmlspecialchars($inn['titre']) ?></td>

                            <!-- ✅ Description -->
                            <td class="description-col">
                                <?= htmlspecialchars($inn['description']) ?>
                            </td>

                            <!-- ✅ Fichier -->
                            <td class="file-col">
                                <?php if (!empty($inn['file'])):

                                    $file = $inn['file'];
                                    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    $fileUrl = "/projet-web/" . ltrim($file, '/');

                                    if (in_array($ext, $imageExtensions)): ?>

                                        <img src="<?= htmlspecialchars($fileUrl) ?>"
                                             class="innovation-img"
                                             alt="Image innovation">

                                    <?php else: ?>

                                        <a href="<?= htmlspecialchars($fileUrl) ?>"
                                           target="_blank"
                                           class="file-link">
                                            📄 Voir le fichier
                                        </a>

                                    <?php endif; ?>

                                <?php else: ?>
                                    <span class="no-file">Aucun fichier</span>
                                <?php endif; ?>
                            </td>

                            <!-- ✅ Catégorie -->
                            <td><?= htmlspecialchars($inn['categorie_nom']) ?></td>

                            <!-- ✅ Statut -->
                            <td><?= htmlspecialchars($inn['statut']) ?></td>

                            <!-- ✅ Date -->
                            <td><?= htmlspecialchars($inn['date_creation']) ?></td>

                            <!-- ✅ Actions -->
                            <td class="actions-cell">

                                <a href="edit_Innovation.php?id=<?= (int)$inn['id'] ?>"
                                   class="btn-icon edit" title="Modifier">✏️</a>

                                <a href="a_Innovation.php?delete=<?= (int)$inn['id'] ?>"
                                   class="btn-icon delete"
                                   onclick="return confirm('Supprimer cette innovation ?');"
                                   title="Supprimer">🗑️</a>

                                <?php if ($inn['statut'] === 'En attente'): ?>

                                    <!-- ✅ Boutons quand c'est en attente -->
                                    <button class="btn-icon validate"
                                            onclick="confirmInnovation(<?= (int)$inn['id'] ?>, 'Validée')"
                                            title="Valider">✅</button>

                                    <button class="btn-icon reject"
                                            onclick="confirmInnovation(<?= (int)$inn['id'] ?>, 'Rejetée')"
                                            title="Refuser">❌</button>

                                <?php elseif ($inn['statut'] === 'Validée'): ?>

                                    <!-- ✅ Icône verte uniquement si vraiment validée -->
                                    <span class="status-valid" title="Innovation validée">✅</span>

                                <?php elseif ($inn['statut'] === 'Rejetée'): ?>

                                    <!-- ❌ Icône rouge uniquement si rejetée -->
                                    <span class="status-rejected" title="Innovation rejetée">❌</span>

                                <?php endif; ?>

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
