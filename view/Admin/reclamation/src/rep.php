<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

require_once __DIR__ . "/../../../../config.php";
require_once __DIR__ . "/../../../../model/login/role.php";

$db = Config::getConnexion();

// ==============================
// RÉCUPÉRATION DES UTILISATEURS
// ==============================
$stmt = $db->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// RÉCUPÉRATION DES RÔLES
// ==============================
$stmtRoles = $db->prepare("SELECT * FROM roles");
$stmtRoles->execute();
$rolesData = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

$roles = [];
foreach ($rolesData as $roleRow) {
    $roles[$roleRow['id']] = $roleRow['nom'];
}

// Résumé des rôles
$roleSummary = [];
foreach ($users as $user) {
    $roleName = $roles[$user['role_id']] ?? 'Inconnu';
    if (!isset($roleSummary[$roleName])) $roleSummary[$roleName] = 0;
    $roleSummary[$roleName]++;
}

// ==============================
// RÉCUPÉRATION DES RÉCLAMATIONS
// ==============================
$stmtRecl = $db->prepare("SELECT * FROM reclamations");
$stmtRecl->execute();
$reclamations = $stmtRecl->fetchAll(PDO::FETCH_ASSOC);

// Sécurité si rien récupéré
if (!is_array($reclamations)) {
    $reclamations = [];
}

$pageTitle     = "👥 Dashboard Utilisateurs";
$pageSubtitle  = "Gestion des comptes et rôles";
$activeMenu    = 'users';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- CSS GLOBAL ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main class="dashboard-inner">
  

    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="liste.php" class="btn-add">
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
        <a href="show.php?id=<?= $id ?>" class="btn-add">
            <i class="bi bi-eye"></i>
            Voir la réclamation
        </a>
        <a href="update.php?id=<?= $id ?>"  class="btn-add" >
            <i class="bi bi-pencil"></i>
            Modifier
        </a>
    </div>

    <div class="response-layout">
        <!-- Informations de la réclamation -->
        <section class="section-box">
            <h3><i class="bi bi-chat-left-text"></i> Réclamation originale</h3>
            <div class="reclamation-info">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Utilisateur:</label>
                        <span><?= htmlspecialchars($reclamation['user']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Sujet:</label>
                        <span><?= htmlspecialchars($reclamation['sujet']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Date:</label>
                        <span><?= htmlspecialchars($reclamation['date']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Statut actuel:</label>
                        <span class="status-badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                            <?= htmlspecialchars($reclamation['statut']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="description-box">
                    <label>Description:</label>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Formulaire de réponse -->
        <section class="section-box">
            <h3><i class="bi bi-reply"></i> Votre réponse</h3>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="admin-form">
                <div class="form-group">
                    <label for="statut">Nouveau statut:</label>
                    <select id="statut" name="statut">
                        <option value="en attente" <?= $reclamation['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="confirmé" <?= $reclamation['statut'] == 'confirmé' ? 'selected' : '' ?>>Confirmé</option>
                        <option value="rejeté" <?= $reclamation['statut'] == 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
                        <option value="résolu" <?= $reclamation['statut'] == 'résolu' ? 'selected' : '' ?>>Résolu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reponse">Message de réponse:</label>
                    <textarea id="reponse" name="reponse" rows="8" placeholder="Tapez votre réponse à l'utilisateur ici..." required></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span> caractères
                    </div>
                </div>

                <div class="response-preview">
                    <h4><i class="bi bi-eye"></i> Aperçu de la réponse</h4>
                    <div class="preview-content" id="previewContent">
                        Votre réponse apparaîtra ici...
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-send"></i>
                        Envoyer la réponse
                    </button>
                    <button type="button" id="previewBtn" class="btn-secondary">
                        <i class="bi bi-eye"></i>
                        Actualiser l'aperçu
                    </button>
                    <a href="liste.php" class="btn-secondary">
                        <i class="bi bi-x-lg"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </section>
    </div>
</main>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/dashboard.js"></script>

<script>
// Compteur de caractères et aperçu en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const reponseTextarea = document.getElementById('reponse');
    const charCount = document.getElementById('charCount');
    const previewContent = document.getElementById('previewContent');
    const previewBtn = document.getElementById('previewBtn');
    
    // Mettre à jour le compteur de caractères
    function updateCharCount() {
        const count = reponseTextarea.value.length;
        charCount.textContent = count;
        
        // Changer la couleur si trop long
        if (count > 1000) {
            charCount.style.color = '#ff6b6b';
        } else if (count > 500) {
            charCount.style.color = '#ffb347';
        } else {
            charCount.style.color = '#4aff8b';
        }
    }
    
    // Mettre à jour l'aperçu
    function updatePreview() {
        const content = reponseTextarea.value.trim();
        if (content) {
            // Convertir les sauts de ligne en balises <br>
            const formattedContent = content.replace(/\n/g, '<br>');
            previewContent.innerHTML = formattedContent;
        } else {
            previewContent.innerHTML = 'Votre réponse apparaîtra ici...';
        }
    }
    
    // Événements
    reponseTextarea.addEventListener('input', updateCharCount);
    reponseTextarea.addEventListener('input', updatePreview);
    previewBtn.addEventListener('click', updatePreview);
    
    // Initialisation
    updateCharCount();
    updatePreview();
});
</script>

</body>
</html>