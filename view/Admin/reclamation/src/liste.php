<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../controller/components/Reclamtion/ReclamationController.php';

$controller = new ReclamationController();

// Récupérer le terme de recherche si présent
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$reclamations = $controller->listReclamations();

// Filtrer les réclamations si un terme de recherche est fourni
if (!empty($searchTerm)) {
    $reclamations = array_filter($reclamations, function($reclamation) use ($searchTerm) {
        // Recherche dans le nom d'utilisateur (insensible à la casse)
        return stripos($reclamation['user'], $searchTerm) !== false;
    });
}

$pageTitle = "📋 Gestion des Réclamations";
$pageSubtitle = empty($searchTerm) 
    ? "Liste complète des réclamations" 
    : "Résultats de recherche pour : " . htmlspecialchars($searchTerm);
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
    
    <style>
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-box {
            flex: 1;
            position: relative;
            max-width: 400px;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
        }
        
        .btn-search {
            padding: 10px 20px;
            background-color: #4361ee;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-search:hover {
            background-color: #3a56d4;
        }
        
        .btn-clear {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-clear:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .search-results-info {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4361ee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .result-count {
            font-weight: 600;
            color: #4361ee;
        }
        
        .quick-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        /* Styles pour l'impression */
        @media print {
            body * {
                visibility: hidden;
            }
            
            .section-box, .section-box * {
                visibility: visible;
            }
            
            .section-box {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }
            
            .section-box table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .section-box th,
            .section-box td {
                border: 1px solid #ddd !important;
                padding: 8px;
            }
            
            .section-box .action-buttons,
            .section-box .search-container,
            .section-box .quick-actions,
            .section-box .search-results-info {
                display: none !important;
            }
            
            /* Ajouter un en-tête d'impression */
            .section-box:before {
                content: "Liste des Réclamations - " attr(data-print-date);
                display: block;
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 20px;
                text-align: center;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
        }
    </style>
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="dashboard.php" class="btn-add">
            <i class="bi bi-arrow-left"></i>
            Retour au Dashboard
        </a>
        
        <div class="search-container">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <form method="GET" action="" style="display: inline;">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Rechercher par utilisateur..."
                           value="<?= htmlspecialchars($searchTerm) ?>"
                           autocomplete="off">
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($searchTerm)): ?>
        <div class="search-results-info">
            <div>
                <span class="result-count"><?= count($reclamations) ?></span> réclamation(s) trouvée(s) pour "<?= htmlspecialchars($searchTerm) ?>"
            </div>
            <a href="liste.php" class="btn-clear" style="padding: 5px 10px; font-size: 12px;">
                <i class="bi bi-x"></i>
                Voir toutes
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Boutons d'export et impression -->
    <?php if (count($reclamations) > 0): ?>
        <div class="section-actions" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button class="btn-add" id="exportBtn" title="Exporter en CSV">
                <i class="bi bi-download"></i>
                Exporter CSV
            </button>
            <button class="btn-add" id="printSectionBtn" title="Imprimer la liste">
                <i class="bi bi-printer"></i>
                Imprimer
            </button>
        </div>
    <?php endif; ?>

    <!-- Tableau des réclamations -->
    <section class="section-box" data-print-date="<?= date('d/m/Y H:i') ?>">
        <?php if (count($reclamations) > 0): ?>
            <div class="table-responsive">
                <table id="reclamationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Sujet</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reclamations as $reclamation): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($reclamation['id']) ?></strong></td>
                            <td>
                                <?php if (!empty($searchTerm)): ?>
                                    <!-- Mettre en surbrillance le terme recherché -->
                                    <?= preg_replace(
                                        '/(' . preg_quote($searchTerm, '/') . ')/i',
                                        '<mark>$1</mark>',
                                        htmlspecialchars($reclamation['user'])
                                    ) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($reclamation['user']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($reclamation['sujet']) ?></td>
                            <td class="description-cell">
                                <?= htmlspecialchars(substr($reclamation['description'], 0, 50)) ?>
                                <?php if (strlen($reclamation['description']) > 50): ?>
                                    <span class="text-muted">...</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($reclamation['date']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                                    <?= htmlspecialchars($reclamation['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="show.php?id=<?= $reclamation['id'] ?>" class="btn-icon" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="update.php?id=<?= $reclamation['id'] ?>" class="btn-icon" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="repondre.php?id=<?= $reclamation['id'] ?>" class="btn-icon" title="Répondre">
                                        <i class="bi bi-reply"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $reclamation['id'] ?>" class="btn-icon btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="bi bi-search" style="font-size: 48px; opacity: 0.5; margin-bottom: 15px;"></i>
                
                <?php if (!empty($searchTerm)): ?>
                    <p>Aucune réclamation trouvée pour "<?= htmlspecialchars($searchTerm) ?>"</p>
                    <p class="text-muted" style="margin-top: 5px; font-size: 14px;">
                        Essayez avec un autre terme de recherche
                    </p>
                    <a href="liste.php" class="btn-add" style="margin-top: 15px;">
                        <i class="bi bi-arrow-left"></i>
                        Voir toutes les réclamations
                    </a>
                <?php else: ?>
                    <p>Aucune réclamation trouvée</p>
                    <a href="ajouter.php" class="btn-add">Créer la première réclamation</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/dashboard.js"></script>

<script>
    // Soumettre automatiquement le formulaire lors de la saisie (avec délai)
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500); // 500ms de délai
        });
        
        // Mettre le focus sur le champ de recherche
        searchInput.focus();
        
        // Permettre de soumettre avec Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
        
        // Export CSV
        document.getElementById('exportBtn')?.addEventListener('click', function() {
            exportToCSV();
        });
        
        // Impression de la section
        document.getElementById('printSectionBtn')?.addEventListener('click', function() {
            printSection();
        });
    });
    
    // Fonction pour exporter en CSV
    function exportToCSV() {
        const table = document.getElementById('reclamationsTable');
        if (!table) {
            alert('Aucune donnée à exporter');
            return;
        }
        
        const rows = table.querySelectorAll('tr');
        if (rows.length <= 1) {
            alert('Aucune donnée à exporter');
            return;
        }
        
        const csvData = [];
        
        // Ajouter l'en-tête (sans la colonne Actions)
        const headerCells = rows[0].querySelectorAll('th');
        const headerRow = [];
        for (let i = 0; i < headerCells.length - 1; i++) {
            headerRow.push(headerCells[i].textContent.trim());
        }
        csvData.push(headerRow.join(','));
        
        // Ajouter les données (sans la colonne Actions)
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].querySelectorAll('td');
            const rowData = [];
            
            for (let j = 0; j < cells.length - 1; j++) { // -1 pour exclure la colonne Actions
                let cellText = cells[j].textContent.trim();
                
                // Nettoyer le texte (supprimer les balises HTML)
                cellText = cellText.replace(/<[^>]*>/g, '');
                
                // Échapper les guillemets
                cellText = cellText.replace(/"/g, '""');
                
                // Ajouter des guillemets si nécessaire
                if (cellText.includes(',') || cellText.includes('"') || cellText.includes('\n')) {
                    cellText = `"${cellText}"`;
                }
                
                rowData.push(cellText);
            }
            
            csvData.push(rowData.join(','));
        }
        
        // Créer le contenu CSV
        const csvContent = csvData.join('\n');
        
        // Créer et télécharger le fichier
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `reclamations_liste_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Libérer l'URL
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }
    
    // Fonction pour imprimer la section
    function printSection() {
        window.print();
    }
</script>

</body>
</html>