[file name]: reponse.php
[file content begin]
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header('Location: ../../Client/login.html');
    exit;
}

include_once __DIR__ . '/../../../../config.php';
include_once __DIR__ . '/../../../../controller/components/Reclamtion/ReclamationController.php';

// Connexion à la base de données pour récupérer les réponses
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=tunispace_database;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

$controller = new ReclamationController();
$reclamations = $controller->listReclamations();

// Récupérer les réponses de la base de données
$reponsesParReclamation = [];
try {
    // Vérifier si la table reponses existe
    $tableExists = $pdo->query("SHOW TABLES LIKE 'reponses'")->rowCount() > 0;
    
    if ($tableExists) {
        $stmt = $pdo->prepare("
            SELECT 
                r.id as reclamation_id,
                rp.contenu as reponse_contenu,
                rp.date_creation as reponse_date,
                u.pseudo as admin_name
            FROM reponses rp
            JOIN reclamations r ON rp.reclamation_id = r.id
            LEFT JOIN user u ON rp.admin_id = u.id
            ORDER BY rp.date_creation DESC
        ");
        $stmt->execute();
        $reponsesDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organiser les réponses par ID de réclamation
        foreach ($reponsesDB as $reponse) {
            $reclamationId = $reponse['reclamation_id'];
            if (!isset($reponsesParReclamation[$reclamationId])) {
                $reponsesParReclamation[$reclamationId] = [];
            }
            $reponsesParReclamation[$reclamationId][] = [
                'contenu' => $reponse['reponse_contenu'],
                'date' => $reponse['reponse_date'],
                'admin' => $reponse['admin_name']
            ];
        }
    }
} catch (PDOException $e) {
    // Si la table n'existe pas encore, on continue avec un tableau vide
    $reponsesParReclamation = [];
}

// Filtrer les réclamations qui ont des réponses
$reclamationsAvecReponses = [];
foreach ($reclamations as $reclamation) {
    $id = $reclamation['id'];
    if (isset($reponsesParReclamation[$id]) && !empty($reponsesParReclamation[$id])) {
        // Ajouter la dernière réponse à la réclamation
        $derniereReponse = end($reponsesParReclamation[$id]);
        $reclamation['derniere_reponse'] = $derniereReponse['contenu'];
        $reclamation['date_reponse'] = $derniereReponse['date'];
        $reclamation['admin_reponse'] = $derniereReponse['admin'];
        $reclamation['toutes_reponses'] = $reponsesParReclamation[$id];
        $reclamationsAvecReponses[] = $reclamation;
    }
}

// Réclamations en attente (sans réponse)
$reclamationsEnAttente = [];
foreach ($reclamations as $reclamation) {
    if (!isset($reponsesParReclamation[$reclamation['id']]) && $reclamation['statut'] == 'en attente') {
        $reclamationsEnAttente[] = $reclamation;
    }
}

// Gestion de la recherche par email
$rechercheEmail = '';
$reclamationsFiltrees = $reclamationsAvecReponses;

if (isset($_GET['search_email']) && !empty($_GET['search_email'])) {
    $rechercheEmail = trim($_GET['search_email']);
    
    // Filtrer les réclamations par email de l'utilisateur
    $reclamationsFiltrees = array_filter($reclamationsAvecReponses, function($reclamation) use ($rechercheEmail) {
        // Vérifier si la clé 'email' existe, sinon chercher dans 'user' qui pourrait contenir l'email
        if (isset($reclamation['email'])) {
            return stripos($reclamation['email'], $rechercheEmail) !== false;
        } elseif (isset($reclamation['user'])) {
            // Si 'user' contient l'email (format: "nom (email@domaine.com)")
            return stripos($reclamation['user'], $rechercheEmail) !== false;
        }
        return false;
    });
    
    // Réindexer le tableau
    $reclamationsFiltrees = array_values($reclamationsFiltrees);
}

$pageTitle = "Reclamations avec Réponses";
$pageSubtitle = "Historique des réclamations traitées";
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
        /* Styles spécifiques pour les réponses */
        .reponse-preview {
            max-width: 300px;
        }
        
        .reponse-text {
            display: block;
            color: #495057;
            font-size: 14px;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .reponse-full {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .reponse-full.active {
            display: block;
        }
        
        .reponse-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .reponse-overlay.active {
            display: block;
        }
        
        .btn-view-full {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            padding: 0;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .btn-view-full:hover {
            text-decoration: underline;
        }
        
        .reponse-meta {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .reponse-meta i {
            margin-right: 3px;
        }
        
        /* Styles pour la barre de recherche */
        .search-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input-group {
            flex: 1;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .btn-search {
            background-color: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-search:hover {
            background-color: #5a67d8;
        }
        
        .btn-clear {
            background-color: #e2e8f0;
            color: #4a5568;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-clear:hover {
            background-color: #cbd5e0;
        }
        
        .search-results-info {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 14px;
            color: #495057;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-results-info.hidden {
            display: none;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .no-results i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>

<body class="admin-dashboard with-sidebar">

<?php include __DIR__ . "/../../layout/sidebar.php"; ?>
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <!-- En-tête de page -->
    <div class="page-header">
        <h1 class="section-title-main"><?= htmlspecialchars($pageTitle) ?></h1>
        <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle) ?></p>
    </div>

    <!-- Barre de recherche par email -->
    <div class="search-container">
        <form method="GET" class="search-form">
            <div class="search-input-group">
                <i class="bi bi-envelope search-icon"></i>
                <input type="email" 
                       name="search_email" 
                       class="search-input" 
                       placeholder="Rechercher par adresse email..." 
                       value="<?= htmlspecialchars($rechercheEmail) ?>"
                       autocomplete="off">
            </div>
            <button type="submit" class="btn-search">
                <i class="bi bi-search"></i> Rechercher
            </button>
            <?php if (!empty($rechercheEmail)): ?>
                <a href="reponse.php" class="btn-clear">
                    <i class="bi bi-x-circle"></i> Effacer
                </a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($rechercheEmail)): ?>
            <div class="search-results-info" id="searchResultsInfo">
                <div>
                    <i class="bi bi-info-circle"></i>
                    Résultats de la recherche pour l'email : 
                    <strong>"<?= htmlspecialchars($rechercheEmail) ?>"</strong>
                    - 
                    <?= count($reclamationsFiltrees) ?> réclamation(s) trouvée(s)
                </div>
                <div>
                    <a href="reponse.php" class="btn-clear btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Tout afficher
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

	<!-- Actions rapides -->
	<div class="quick-actions">
	    <a href="liste.php" class="btn-add">
	        <i class="bi bi-list-ul"></i>
	        Toutes les Réclamations
	    </a>
	    <a href="dashboard.php" class="btn-add">
	        <i class="bi bi-speedometer2"></i>
	        Dashboard
	    </a>
	    <?php if (!empty($reclamationsAvecReponses)): ?>
	    <button id="exportBtn" class="btn-add">
	        <i class="bi bi-download"></i>
	        Exporter le tableau
	    </button>
	    <?php endif; ?>
	</div>
    <!-- Filtres -->
    <div class="section-box">
        <div class="filter-section">
            <h3><i class="bi bi-funnel"></i> Filtres</h3>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Toutes les réponses</button>
                <button class="filter-btn" data-filter="résolu">Résolues</button>
                <button class="filter-btn" data-filter="confirmé">Confirmées</button>
                <button class="filter-btn" data-filter="rejeté">Rejetées</button>
                <button class="filter-btn" data-filter="en cours">En cours</button>
            </div>
        </div>
    </div>

    <!-- Liste des réclamations avec réponses -->
    <section class="section-box">
        <div class="section-header">
            <h2><i class="bi bi-chat-dots"></i> 
                Réclamations avec Réponses 
                <?php if (empty($rechercheEmail)): ?>
                    (<?= count($reclamationsAvecReponses) ?>)
                <?php else: ?>
                    <span style="font-size: 0.8em; color: #667eea;">
                        (<?= count($reclamationsFiltrees) ?> résultat(s) pour "<?= htmlspecialchars($rechercheEmail) ?>")
                    </span>
                <?php endif; ?>
            </h2>
        </div>

        <?php if (count($reclamationsFiltrees) > 0): ?>
            <div class="table-responsive">
                <table class="reponses-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Date Réclamation</th>
                            <th>Statut</th>
                            <th>Dernière Réponse</th>
                            <th>Date Réponse</th>
                            <th>Par</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reclamationsFiltrees as $reclamation): 
                            $derniereReponse = $reclamation['derniere_reponse'] ?? '';
                            $dateReponse = $reclamation['date_reponse'] ?? '';
                            $adminReponse = $reclamation['admin_reponse'] ?? 'Administrateur';
                            $reponseId = $reclamation['id'];
                            
                            // Extraire l'email de l'utilisateur si possible
                            $emailUtilisateur = '';
                            if (isset($reclamation['email'])) {
                                $emailUtilisateur = $reclamation['email'];
                            } elseif (isset($reclamation['user'])) {
                                // Tenter d'extraire l'email du format "nom (email@domaine.com)"
                                $userInfo = $reclamation['user'];
                                if (preg_match('/\(([^)]+@[^)]+)\)/', $userInfo, $matches)) {
                                    $emailUtilisateur = $matches[1];
                                }
                            }
                        ?>
                        <tr class="reponse-item" data-statut="<?= htmlspecialchars($reclamation['statut']) ?>" data-reclamation-id="<?= $reclamation['id'] ?>" data-email="<?= htmlspecialchars($emailUtilisateur) ?>">
                            <td><strong>#<?= htmlspecialchars($reclamation['id']) ?></strong></td>
                            <td>
                                <div class="user-info">
                                    <i class="bi bi-person-circle"></i>
                                    <?= htmlspecialchars($reclamation['user']) ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($emailUtilisateur)): ?>
                                    <div class="email-info">
                                        <i class="bi bi-envelope"></i>
                                        <?= htmlspecialchars($emailUtilisateur) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="sujet-cell">
                                    <?= htmlspecialchars($reclamation['sujet']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($reclamation['date']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($reclamation['statut']) ?>">
                                    <i class="bi <?= getStatusIcon($reclamation['statut']) ?>"></i>
                                    <?= htmlspecialchars($reclamation['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="reponse-preview">
                                    <?php if (!empty($derniereReponse)): ?>
                                        <span class="reponse-text"><?= htmlspecialchars(substr($derniereReponse, 0, 60)) ?>...</span>
                                        <button class="btn-view-full" onclick="afficherReponseComplete(<?= $reclamation['id'] ?>)">
                                            Voir plus
                                        </button>
                                        <div class="reponse-meta">
                                            <i class="bi bi-chat-text"></i>
                                            <?= count($reclamation['toutes_reponses']) ?> réponse(s)
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Aucune réponse</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($dateReponse)): ?>
                                    <?= date('d/m/Y H:i', strtotime($dateReponse)) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-info">
                                    <i class="bi bi-person-badge"></i>
                                    <?= htmlspecialchars($adminReponse) ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="show.php?id=<?= $reclamation['id'] ?>" class="btn-icon" title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="repondre.php?id=<?= $reclamation['id'] ?>" class="btn-icon" title="Modifier la réponse">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="liste.php" class="btn-icon" title="Voir dans la liste">
                                        <i class="bi bi-list-ul"></i>
                                    </a>
                                    <button class="btn-icon" title="Voir toutes les réponses" onclick="afficherReponseComplete(<?= $reclamation['id'] ?>)">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn disabled">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        <?php elseif (!empty($rechercheEmail)): ?>
            <div class="no-results">
                <i class="bi bi-envelope-exclamation"></i>
                <h3>Aucune réclamation trouvée</h3>
                <p>Aucune réclamation avec réponse ne correspond à l'email "<?= htmlspecialchars($rechercheEmail) ?>"</p>
                <div style="margin-top: 20px;">
                    <a href="reponse.php" class="btn-primary">
                        <i class="bi bi-arrow-left"></i>
                        Retour à toutes les réclamations
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="bi bi-chat-square-text" style="font-size: 64px; opacity: 0.5; margin-bottom: 20px;"></i>
                <h3>Aucune réclamation avec réponse</h3>
                <p>Les réclamations auxquelles vous avez répondu apparaîtront ici.</p>
                <a href="liste.php" class="btn-primary">
                    <i class="bi bi-arrow-right"></i>
                    Voir les réclamations en attente
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Réclamations en attente (section bonus) -->
    <?php if (count($reclamationsEnAttente) > 0 && empty($rechercheEmail)): ?>
    <section class="section-box">
        <h3><i class="bi bi-clock"></i> Réclamations en Attente de Réponse (<?= count($reclamationsEnAttente) ?>)</h3>
        <div class="pending-list">
            <?php 
            $pendingPreview = array_slice($reclamationsEnAttente, 0, 3);
            foreach($pendingPreview as $reclamation): 
            ?>
            <div class="pending-item">
                <div class="pending-info">
                    <strong>#<?= htmlspecialchars($reclamation['id']) ?> - <?= htmlspecialchars($reclamation['sujet']) ?></strong>
                    <span class="pending-user">par <?= htmlspecialchars($reclamation['user']) ?></span>
                    <span class="pending-date"><?= htmlspecialchars($reclamation['date']) ?></span>
                </div>
                <a href="repondre.php?id=<?= $reclamation['id'] ?>" class="btn-primary btn-sm">
                    <i class="bi bi-reply"></i>
                    Répondre
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if (count($reclamationsEnAttente) > 3): ?>
            <div class="pending-more">
                <a href="liste.php" class="btn-dashboard">
                    Voir les <?= count($reclamationsEnAttente) - 3 ?> autres réclamations en attente
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
</main>

<!-- Modale pour afficher la réponse complète -->
<div id="reponseOverlay" class="reponse-overlay"></div>
<div id="reponseFull" class="reponse-full">
    <div class="reponse-header">
        <h3>Réponse(s) complète(s)</h3>
        <button onclick="fermerReponseComplete()" class="btn-close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div id="reponseContent" class="reponse-content">
        <!-- Le contenu sera injecté ici par JavaScript -->
    </div>
</div>

<!-- JS GLOBAL ADMIN -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/dashboard.js"></script>

<script>
// Données des réponses pour JavaScript
const reponsesData = <?php echo json_encode($reclamationsFiltrees); ?>;

// Fonction pour obtenir l'icône selon le statut
function getStatusIcon(statut) {
    const icons = {
        'résolu': 'bi-check-circle',
        'confirmé': 'bi-check-lg',
        'rejeté': 'bi-x-circle',
        'en cours': 'bi-arrow-repeat',
        'en attente': 'bi-clock'
    };
    return icons[statut] || 'bi-question-circle';
}

// Afficher la réponse complète
function afficherReponseComplete(reclamationId) {
    // Trouver la réclamation correspondante
    const reclamation = reponsesData.find(r => r.id == reclamationId);
    
    if (!reclamation) return;
    
    // Construire le contenu HTML
    let html = `
        <div class="reclamation-info">
            <h4>Réclamation #${reclamation.id}</h4>
            <p><strong>Utilisateur:</strong> ${reclamation.user}</p>
            <p><strong>Statut:</strong> ${reclamation.statut}</p>
            <hr>
        </div>
    `;
    
    // Ajouter toutes les réponses
    if (reclamation.toutes_reponses && reclamation.toutes_reponses.length > 0) {
        html += `<h4>${reclamation.toutes_reponses.length} Réponse(s)</h4>`;
        
        reclamation.toutes_reponses.forEach((rep, index) => {
            const date = new Date(rep.date).toLocaleString('fr-FR');
            html += `
                <div class="reponse-complete">
                    <div class="reponse-header">
                        <strong>Réponse #${index + 1}</strong>
                        <span class="reponse-meta">
                            <i class="bi bi-calendar"></i> ${date}
                            <i class="bi bi-person"></i> ${rep.admin || 'Administrateur'}
                        </span>
                    </div>
                    <div class="reponse-contenu">
                        ${rep.contenu.replace(/\n/g, '<br>')}
                    </div>
                    <hr>
                </div>
            `;
        });
    } else {
        html += `<p class="text-muted">Aucune réponse disponible</p>`;
    }
    
    // Injecter le contenu
    document.getElementById('reponseContent').innerHTML = html;
    
    // Afficher la modale
    document.getElementById('reponseOverlay').classList.add('active');
    document.getElementById('reponseFull').classList.add('active');
}

// Fermer la réponse complète
function fermerReponseComplete() {
    document.getElementById('reponseOverlay').classList.remove('active');
    document.getElementById('reponseFull').classList.remove('active');
}

// Filtrage des réponses
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const reponseItems = document.querySelectorAll('.reponse-item');
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Retirer la classe active de tous les boutons
            filterButtons.forEach(b => b.classList.remove('active'));
            // Ajouter la classe active au bouton cliqué
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filtrer les éléments
            reponseItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-statut') === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
// Export du tableau en CSV (version simplifiée)
document.getElementById('exportBtn')?.addEventListener('click', function() {
    if (reponsesData.length === 0) {
        alert('Aucune donnée à exporter');
        return;
    }
    
    // Créer un tableau pour l'export
    const data = [];
    reponsesData.forEach(reclamation => {
        // Extraire l'email de l'utilisateur
        let emailUtilisateur = '';
        if (reclamation.email) {
            emailUtilisateur = reclamation.email;
        } else if (reclamation.user && reclamation.user.includes('@')) {
            // Tentative d'extraction de l'email
            const match = reclamation.user.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/);
            if (match) emailUtilisateur = match[0];
        }
        
        data.push({
            ID: reclamation.id,
            Utilisateur: reclamation.user.replace(/\([^)]*\)/g, '').trim(), // Enlever l'email entre parenthèses
            Email: emailUtilisateur,
            Sujet: reclamation.sujet,
            'Date Réclamation': reclamation.date,
            Statut: reclamation.statut,
            'Dernière Réponse': reclamation.derniere_reponse?.substring(0, 100) + (reclamation.derniere_reponse?.length > 100 ? '...' : '') || '',
            'Date Réponse': reclamation.date_reponse || '',
            'Administrateur': reclamation.admin_reponse || ''
        });
    });
    
    // Convertir en CSV
    const headers = Object.keys(data[0]);
    const csv = [
        headers.join(','),
        ...data.map(row => headers.map(header => {
            const cell = row[header] || '';
            return `"${cell.toString().replace(/"/g, '""')}"`;
        }).join(','))
    ].join('\n');
    
    // Créer et télécharger le fichier
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `reclamations_avec_reponses_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});    // Fermer la modale en cliquant sur l'overlay
    document.getElementById('reponseOverlay').addEventListener('click', fermerReponseComplete);
    
    // Auto-focus sur le champ de recherche
    document.querySelector('.search-input')?.focus();
    
    // Recherche en temps réel (optionnel)
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
</script>

</body>
</html>

<?php
// Fonction helper pour les icônes de statut
function getStatusIcon($statut) {
    $icons = [
        'résolu' => 'bi-check-circle',
        'confirmé' => 'bi-check-lg',
        'rejeté' => 'bi-x-circle',
        'en cours' => 'bi-arrow-repeat',
        'en attente' => 'bi-clock'
    ];
    return $icons[$statut] ?? 'bi-question-circle';
}
?>
[file content end]