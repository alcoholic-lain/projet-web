<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../controller/components/Reclamtion/ReclamationController.php';

$error = "";
$success = "";

// Récupérer l'ID de la réclamation
$id = $_GET['id'] ?? 0;
$controller = new ReclamationController();
$reclamation = $controller->getReclamation($id);

if (!$reclamation) {
    die("Réclamation non trouvée");
}

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=tunispace_database;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Traitement du formulaire de réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reponse = trim($_POST['reponse'] ?? '');
    $statut = $_POST['statut'] ?? $reclamation['statut'];
    
    if (empty($reponse)) {
        $error = "Le message de réponse est obligatoire.";
    } else {
        try {
            // Commencer une transaction
            $pdo->beginTransaction();
            
            // 1. Mettre à jour le statut de la réclamation
            $stmt = $pdo->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
            $stmt->execute([$statut, $id]);
            
            // 2. Insérer la réponse dans la table reponses
            $stmt = $pdo->prepare("
                INSERT INTO reponses (reclamation_id, admin_id, contenu, date_creation) 
                VALUES (?, ?, ?, NOW())
            ");
            
            // Utiliser l'ID de l'admin connecté
            $admin_id = $_SESSION['user_id'];
            $stmt->execute([$id, $admin_id, $reponse]);
            
            // Valider la transaction
            $pdo->commit();
            
            $success = "Réponse envoyée avec succés ! Statut mis à jour.";
            
            // Recharger les données de la réclamation
            $reclamation = $controller->getReclamation($id);
            
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            $error = "Erreur lors de l'enregistrement: " . $e->getMessage();
        }
    }
}

$pageTitle = " Répondre à la Réclamation #$id";
$pageSubtitle = "Envoyer une réponse à l'utilisateur";
$activeMenu = 'reclamations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- CSS GLOBAL ADMIN -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÃ‰CIFIQUE -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <link rel="stylesheet" href="../assets/css/ai.css">

    <!-- ICONES BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <!-- POLICE INTER -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main >
    <!-- En-tÃªte de page -->
    <div class="page-header">
        <h1 class="section-title-main"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle) ?></p>
    </div>

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
        <a href="update.php?id=<?= $id ?>" class="btn-add">
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
            
            <!-- Afficher les réponses existantes -->
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT r.*, u.pseudo as admin_name 
                    FROM reponses r 
                    LEFT JOIN user u ON r.admin_id = u.id 
                    WHERE r.reclamation_id = ? 
                    ORDER BY r.date_creation ASC
                ");
                $stmt->execute([$id]);
                $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($reponses)): ?>
                    <div class="existing-responses">
                        <h4><i class="bi bi-chat-dots"></i> Réponses précédentes</h4>
                        <?php foreach($reponses as $rep): ?>
                            <div class="reponse-card">
                                <div class="reponse-header">
                                    <div class="reponse-author">
                                        <i class="bi bi-person-circle"></i>
                                        <?= htmlspecialchars($rep['admin_name'] ?? 'Administrateur') ?>
                                    </div>
                                    <div class="reponse-date">
                                        <i class="bi bi-clock"></i>
                                        <?= date('d/m/Y à H:i', strtotime($rep['date_creation'])) ?>
                                    </div>
                                </div>
                                <div class="reponse-content">
                                    <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif;
            } catch (PDOException $e) {
                // Ignorer l'erreur si la table n'existe pas encore
            }
            ?>
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
                <!-- Champs cachés pour l'IA -->
                <input type="hidden" name="reclamation_sujet" value="<?= htmlspecialchars($reclamation['sujet']) ?>">
                <input type="hidden" name="reclamation_description" value="<?= htmlspecialchars($reclamation['description']) ?>">
                <input type="hidden" name="reclamation_statut" value="<?= htmlspecialchars($reclamation['statut']) ?>">
                <input type="hidden" name="reclamation_user" value="<?= htmlspecialchars($reclamation['user']) ?>">
                
                <div class="form-group">
                    <label for="statut">Nouveau statut:</label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="en attente" <?= $reclamation['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="en cours" <?= $reclamation['statut'] == 'en cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="confirmé" <?= $reclamation['statut'] == 'confirmé' ? 'selected' : '' ?>>Confirmé</option>
                        <option value="rejeté" <?= $reclamation['statut'] == 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
                        <option value="résolu" <?= $reclamation['statut'] == 'résolu' ? 'selected' : '' ?>>Résolu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reponse">Message de réponse:</label>
                    
                    <!-- Boutons d'IA -->
                   
<div class="ai-actions">
    <button type="button" id="aiGenerate" class="btn-ai">
        <i class="bi bi-magic"></i>
        Reponse Rapide
    </button>
    <button type="button" id="aiTranslate" class="btn-ai">
        <i class="bi bi-translate"></i>
        Traduire
    </button>
</div>                    
 <!-- Boutons d'IA -->
                    <textarea id="reponse" name="reponse" rows="8" 
                              placeholder="Tapez votre réponse à l'utilisateur ici..." 
                              required><?= isset($_POST['reponse']) ? htmlspecialchars($_POST['reponse']) : '' ?></textarea>
                    
                    <!-- Options de ton pour l'IA -->
                    <div id="toneOptions" style="display: none;">
                        <button type="button" data-tone="professionnel" class="btn-add">Professionnel</button>
                        <button type="button" data-tone="amical" class="btn-add">Amical</button>
                        <button type="button" data-tone="empathique" class="btn-add">Empathique</button>
                        <button type="button" data-tone="formel" class="btn-add">Formel</button>
                        <button type="button" data-tone="simple" class="btn-add">Simple et clair</button>
                    </div>
                    
                    <!-- Options de langue pour la traduction -->
                    <div id="languageOptions"  style="display: none;">
                        <button type="button" data-lang="fr" class="btn-add">Francais</button>
                        <button type="button" data-lang="en" class="btn-add">Anglais</button>
                        <button type="button" data-lang="ar" class="btn-add">Arabe</button>
                    </div>
                    
                    <div class="char-count">
                        <span id="charCount">0</span> caracteres
                        <span id="charLimit" class="char-limit">(Limite: 2000 caracteres)</span>
                    </div>
                    
                    <!-- Indicateur de chargement IA -->
                    <div id="aiLoading" class="ai-loading" style="display: none;">
                        <div class="ai-spinner"></div>
                        <span>L'IA réfléchit...</span>
                    </div>
                </div>

                <!-- AperÃ§u en temps réel -->
                <div class="form-group">
                    <button type="button" id="previewBtn" class="btn-preview">
                        <i class="bi bi-eye"></i>
                        Apercue
                    </button>
                    <div id="previewArea" class="preview-area" style="display: none;">
                        <label>AperÃ§u de votre réponse:</label>
                        <div id="previewContent" class="preview-content"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-send"></i>
                        Envoyer la réponse
                    </button>
                    
                    <button type="reset" class="btn-secondary">
                        <i class="bi bi-eraser"></i>
                        Effacer
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

<!-- JS SPÃ‰CIFIQUE -->
<script src="../assets/js/dashboard.js"></script>

<script src="../assets/js/ai.js"></script>