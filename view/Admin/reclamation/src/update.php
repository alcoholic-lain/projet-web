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

// Récupérer la réclamation à modifier
$id = $_GET['id'] ?? 0;
$controller = new ReclamationController();
$reclamation = $controller->getReclamation($id);

if (!$reclamation) {
    die("Réclamation non trouvée");
}

// Fonction de validation
function validateReclamation($user, $sujet, $description) {
    $errors = [];

    if (empty(trim($user))) {
        $errors[] = "Le nom de l'utilisateur est obligatoire.";
    }

    if (empty(trim($sujet))) {
        $errors[] = "Le sujet est obligatoire.";
    }

    if (empty(trim($description))) {
        $errors[] = "La description est obligatoire.";
    }

    return $errors;
}

// Traitement du formulaire
if ($_POST) {
    $user = $_POST['user'] ?? '';
    $sujet = $_POST['sujet'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';

    // Validation
    $errors = validateReclamation($user, $sujet, $description);

    if (empty($errors)) {
        if ($controller->updateReclamation($id, $user, $sujet, $description, $statut)) {
            $success = "Réclamation modifiée avec succès !";
            // Recharger les données
            $reclamation = $controller->getReclamation($id);
        } else {
            $error = "Erreur lors de la modification de la réclamation.";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

$pageTitle = "✏️ Modifier Réclamation #$id";
$pageSubtitle = "Modification des informations de la réclamation";
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

    <!-- CSS SPÉCIFIQUE -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- ICONES BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <!-- POLICE INTER -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main >


    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="liste.php" class="btn-add" >
            <i class="bi bi-arrow-left"></i>
            Retour à la liste
        </a>
        <a href="show.php?id=<?= $id ?>" class="btn-add">
            <i class="bi bi-eye"></i>
            Voir la réclamation
        </a>
    </div>
    <div class="section-actions">
             <button class="btn-icon" id="printBtn" title="Imprimer uniquement les d�tails" style="margin-top:10px;">
				    <i class="bi bi-printer"></i>
				</button>
        </div>
    <!-- Formulaire -->
    <section class="section-box">
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
                <label for="user">Utilisateur:</label>
                <input type="text" id="user" name="user" value="<?= htmlspecialchars($reclamation['user']) ?>" required>
            </div>

            <div class="form-group">
                <label for="sujet">Sujet:</label>
                <input type="text" id="sujet" name="sujet" value="<?= htmlspecialchars($reclamation['sujet']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($reclamation['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="statut">Statut:</label>
                <select id="statut" name="statut">
                    <option value="en attente" <?= $reclamation['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="confirmé" <?= $reclamation['statut'] == 'confirmé' ? 'selected' : '' ?>>Confirmé</option>
                    <option value="rejeté" <?= $reclamation['statut'] == 'rejeté' ? 'selected' : '' ?>>Rejeté</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">
                    <i class="bi bi-check-lg"></i>
                    Modifier la réclamation
                </button>
                <a href="liste.php" class="btn-add">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
            </div>
        </form>
    </section>
</main>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/dashboard.js"></script>
<script>
    // Export et impression
    document.getElementById('exportBtn')?.addEventListener('click', function() {
        alert('Fonction d\'export � impl�menter');
    });
    
    document.getElementById('printBtn')?.addEventListener('click', function() {
        // Sauvegarder le contenu original de la page
        var originalContent = document.body.innerHTML;
        
        // R�cup�rer le contenu de la section � imprimer
        var sectionContent = document.querySelector('.section-box').outerHTML;
        
        // R�cup�rer le titre de la page
        var pageTitle = "<?= htmlspecialchars($pageTitle) ?>";
        
        // Construire le document HTML pour l'impression
        var printContent = `
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>${pageTitle}</title>
                <style>
                    @media print {
                        @page {
                            margin: 20mm;
                        }
                        body {
                            font-family: 'Inter', sans-serif;
                            color: #333;
                            line-height: 1.6;
                            padding: 20px;
                        }
                        h1 {
                            color: #2c3e50;
                            border-bottom: 2px solid #3498db;
                            padding-bottom: 10px;
                            margin-bottom: 30px;
                        }
                        .detail-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                            gap: 15px;
                            margin: 20px 0;
                        }
                        .detail-item {
                            margin-bottom: 15px;
                            padding: 10px;
                            border-bottom: 1px solid #eee;
                        }
                        .detail-item label {
                            font-weight: bold;
                            color: #555;
                            display: block;
                            margin-bottom: 5px;
                            font-size: 14px;
                        }
                        .detail-value {
                            font-size: 16px;
                            color: #333;
                        }
                        .full-width {
                            grid-column: 1 / -1;
                        }
                        .detail-description {
                            background: #f9f9f9;
                            padding: 15px;
                            border-radius: 8px;
                            border-left: 4px solid #3498db;
                            margin-top: 5px;
                            line-height: 1.8;
                        }
                        .status-badge {
                            display: inline-block;
                            padding: 5px 15px;
                            border-radius: 20px;
                            font-size: 14px;
                            font-weight: 600;
                            margin-top: 5px;
                        }
                        .status-en_attente { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
                        .status-en_cours { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
                        .status-resolu { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
                        .status-ferme { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                        
                        /* Cacher les �l�ments non n�cessaires */
                        .detail-actions,
                        .btn-add,
                        .quick-actions,
                        .section-actions,
                        .btn-icon,
                        button,
                        a {
                            display: none !important;
                        }
                    }
                    
                    /* Styles pour la pr�visualisation */
                    body {
                        font-family: 'Inter', sans-serif;
                        color: #333;
                        line-height: 1.6;
                        padding: 40px;
                        max-width: 900px;
                        margin: 0 auto;
                        background: white;
                    }
                    h1 {
                        color: #2c3e50;
                        border-bottom: 2px solid #3498db;
                        padding-bottom: 15px;
                        margin-bottom: 40px;
                        text-align: center;
                    }
                    .detail-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 20px;
                        margin: 30px 0;
                    }
                    .detail-item {
                        margin-bottom: 20px;
                        padding: 15px;
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    .detail-item label {
                        font-weight: bold;
                        color: #555;
                        display: block;
                        margin-bottom: 8px;
                        font-size: 14px;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .detail-value {
                        font-size: 16px;
                        color: #333;
                        font-weight: 500;
                    }
                    .full-width {
                        grid-column: 1 / -1;
                    }
                    .detail-description {
                        background: #f8f9fa;
                        padding: 20px;
                        border-radius: 8px;
                        border-left: 4px solid #3498db;
                        margin-top: 10px;
                        line-height: 1.8;
                        font-size: 15px;
                    }
                    .status-badge {
                        display: inline-block;
                        padding: 8px 20px;
                        border-radius: 25px;
                        font-size: 14px;
                        font-weight: 600;
                        margin-top: 8px;
                    }
                </style>
            </head>
            <body>
                <h1>${pageTitle}</h1>
                ${sectionContent}
                
                <script>
                    // D�clencher l'impression automatiquement
                    window.onload = function() {
                        window.focus();
                        setTimeout(function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 100);
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `;
        
        // Ouvrir une nouvelle fen�tre pour l'impression
        var printWindow = window.open('', '_blank', 'width=900,height=600');
        printWindow.document.open();
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        // Pour la pr�visualisation avant impression
        printWindow.focus();
    }); 
</script></body>
</html>