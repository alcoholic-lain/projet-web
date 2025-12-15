<?php
// ticket.php
session_start();

// Connexion à la base de données
$host = '127.0.0.1';
$dbname = 'tunispace_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID de la réclamation depuis l'URL
$reclamation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations de la réclamation
$reclamation = null;
$reponses = [];

if ($reclamation_id > 0) {
    try {
        // Récupérer la réclamation avec l'email de l'utilisateur
        $stmt = $pdo->prepare("
            SELECT r.*, u.email 
            FROM reclamations r 
            LEFT JOIN user u ON r.user = u.pseudo 
            WHERE r.id = ?
        ");
        $stmt->execute([$reclamation_id]);
        $reclamation = $stmt->fetch();
        
        // Récupérer les réponses associées
        if ($reclamation) {
            // Vérifier si la table reponses existe
            $tableExists = $pdo->query("SHOW TABLES LIKE 'reponses'")->rowCount() > 0;
            
            if ($tableExists) {
                // Récupérer les réponses avec les informations de l'admin
                $stmt = $pdo->prepare("
                    SELECT 
                        rp.*,
                        u.pseudo as admin_name,
                        u.email as admin_email
                    FROM reponses rp
                    LEFT JOIN user u ON rp.admin_id = u.id
                    WHERE rp.reclamation_id = ? 
                    ORDER BY rp.date_creation ASC
                ");
                $stmt->execute([$reclamation_id]);
                $reponses = $stmt->fetchAll();
            }
        }
    } catch (PDOException $e) {
        $error = "Erreur de base de données: " . $e->getMessage();
    }
}

// Si la réclamation n'existe pas
if (!$reclamation && $reclamation_id > 0) {
    $error = "Réclamation non trouvée.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Réclamation #<?php echo $reclamation_id; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .ticket-info {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .info-badge {
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .ticket-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .ticket-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .ticket-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            display: inline-block;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            word-break: break-word;
        }
        
        .description-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 10px;
            line-height: 1.6;
        }
        
        .reponses-section {
            margin-top: 40px;
        }
        
        .reponse-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #764ba2;
            transition: transform 0.3s ease;
        }
        
        .reponse-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .reponse-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .reponse-author {
            font-weight: 600;
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .reponse-author i {
            font-size: 18px;
        }
        
        .reponse-date {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .reponse-date i {
            font-size: 14px;
        }
        
        .reponse-content {
            line-height: 1.6;
            color: #333;
            white-space: pre-wrap;
            padding: 10px 0;
        }
        
        .no-reponses {
            text-align: center;
            color: #666;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffc107;
            color: #333;
        }
        
        .status-inprogress {
            background: #17a2b8;
            color: white;
        }
        
        .status-resolved {
            background: #28a745;
            color: white;
        }
        
        .status-rejected {
            background: #dc3545;
            color: white;
        }
        
        .status-confirmed {
            background: #007bff;
            color: white;
        }
        
        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .back-button:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .error-message {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 50px auto;
            max-width: 600px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .error-message h3 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        .admin-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .admin-email {
            font-size: 11px;
            color: #868e96;
        }
        
        .reponse-count {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .ticket-content {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .ticket-info {
                flex-direction: column;
                align-items: center;
            }
            
            .reponse-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .reponse-date {
                align-self: flex-end;
            }
        }
    </style>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <h3>❌ Erreur</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="../choix.php" class="back-button">Retour aux réclamations</a>
            </div>
        <?php elseif (!$reclamation && $reclamation_id == 0): ?>
            <div class="error-message">
                <h3>⚠️ Information manquante</h3>
                <p>Aucun ID de réclamation spécifié.</p>
                <p>Veuillez sélectionner une réclamation pour voir les détails.</p>
                <a href="../choix.php" class="back-button">Voir mes réclamations</a>
            </div>
        <?php else: ?>
            <!-- En-tête -->
            <div class="header">
                <h1>🎫 Ticket de Réclamation</h1>
                <div class="ticket-info">
                    <div class="info-badge">ID: #<?php echo htmlspecialchars($reclamation['id']); ?></div>
                    <div class="info-badge">Utilisateur: <?php echo htmlspecialchars($reclamation['user']); ?></div>
                    <div class="info-badge">Date: <?php echo date('d/m/Y H:i', strtotime($reclamation['date'])); ?></div>
                    <?php if (!empty($reponses)): ?>
                        <div class="info-badge">
                            Réponses: <span class="reponse-count"><?php echo count($reponses); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contenu du ticket -->
            <div class="ticket-content">
                <!-- Informations de base -->
                <div class="ticket-section">
                    <h2 class="section-title">Informations de la Réclamation</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">ID Réclamation</div>
                            <div class="info-value">#<?php echo htmlspecialchars($reclamation['id']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($reclamation['email'] ?? 'Non spécifié'); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Statut</div>
                            <div class="info-value">
                                <?php 
                                $status_class = '';
                                switch(strtolower($reclamation['statut'])) {
                                    case 'en attente': $status_class = 'status-pending'; break;
                                    case 'en cours': $status_class = 'status-inprogress'; break;
                                    case 'résolu': $status_class = 'status-resolved'; break;
                                    case 'rejeté': $status_class = 'status-rejected'; break;
                                    case 'confirmé': $status_class = 'status-confirmed'; break;
                                    default: $status_class = 'status-pending';
                                }
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($reclamation['statut']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Date de création</div>
                            <div class="info-value"><?php echo date('d/m/Y à H:i', strtotime($reclamation['date'])); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sujet et Description -->
                <div class="ticket-section">
                    <h2 class="section-title">Détails de la Réclamation</h2>
                    <div class="info-item">
                        <div class="info-label">Sujet</div>
                        <div class="info-value"><?php echo htmlspecialchars($reclamation['sujet']); ?></div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <div class="info-label">Description</div>
                        <div class="description-box">
                            <?php echo nl2br(htmlspecialchars($reclamation['description'])); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Réponses -->
                <div class="ticket-section reponses-section">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                        <h2 class="section-title">Réponses</h2>
                        <?php if (!empty($reponses)): ?>
                            <div style="font-size: 14px; color: #666;">
                                <i class="bi bi-chat-text"></i>
                                <?php echo count($reponses); ?> réponse(s)
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($reponses)): ?>
                        <?php foreach($reponses as $reponse): ?>
                            <div class="reponse-card">
                                <div class="reponse-header">
                                    <div class="reponse-author">
                                        <i class="bi bi-person-badge"></i>
                                        <?php 
                                        // Déterminer qui a répondu
                                        if(!empty($reponse['admin_name'])) {
                                            echo htmlspecialchars($reponse['admin_name']);
                                        } else {
                                            echo "Administrateur";
                                        }
                                        ?>
                                        <div class="admin-info">
                                            <?php if (!empty($reponse['admin_email'])): ?>
                                                <span class="admin-email">
                                                    <i class="bi bi-envelope"></i>
                                                    <?php echo htmlspecialchars($reponse['admin_email']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="reponse-date">
                                        <i class="bi bi-clock"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($reponse['date_creation'])); ?>
                                    </div>
                                </div>
                                <div class="reponse-content">
                                    <?php echo nl2br(htmlspecialchars($reponse['contenu'])); ?>
                                </div>
                                <?php if (!empty($reponse['date_creation'])): ?>
                                    <div style="text-align: right; font-size: 12px; color: #868e96; margin-top: 10px;">
                                        Répondu le <?php echo date('d/m/Y à H:i', strtotime($reponse['date_creation'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-reponses">
                            <i class="bi bi-chat-square-text" style="font-size: 48px; opacity: 0.5; margin-bottom: 15px;"></i>
                            <p style="font-size: 18px; margin-bottom: 10px;">Aucune réponse pour le moment</p>
                            <p style="color: #666;">Votre réclamation est en cours de traitement par notre équipe.</p>
                            <p style="color: #868e96; font-size: 14px; margin-top: 10px;">
                                <i class="bi bi-info-circle"></i>
                                Vous serez notifié dès qu'une réponse sera disponible.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bouton de retour -->
            <div style="text-align: center;">
                <a href="../choix.php" class="back-button">
                    <i class="bi bi-arrow-left"></i>
                    Retour aux réclamations
                </a>
                
                <?php if (!empty($reponses)): ?>
                    <button onclick="window.print()" class="back-button" style="margin-left: 10px; background: #6c757d;">
                        <i class="bi bi-printer"></i>
                        Imprimer le ticket
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Animation pour les cartes de réponse
        document.addEventListener('DOMContentLoaded', function() {
            const reponseCards = document.querySelectorAll('.reponse-card');
            reponseCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate');
            });
            
            // Confirmation d'impression
            const printBtn = document.querySelector('button[onclick="window.print()"]');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    setTimeout(() => {
                        alert('Ticket imprimé avec succès !');
                    }, 100);
                });
            }
        });
    </script>
    
    <style>
        /* Animation pour les cartes */
        .reponse-card.animate {
            animation: slideIn 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Styles d'impression */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .header, .ticket-content {
                box-shadow: none !important;
                background: white !important;
            }
            
            .back-button, .info-badge:last-child {
                display: none !important;
            }
            
            .reponse-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</body>
</html>