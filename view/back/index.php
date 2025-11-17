<?php
// C:\xampp\htdocs\admin\view\back\index.php

// Chemin absolu direct Windows
$base_path = 'C:/xampp/htdocs/admin/';
$config_path = $base_path . 'config.php';
$controller_path = $base_path . 'controller/ReclamationController.php';

echo "<!-- Debug: config_path = $config_path -->";
echo "<!-- Debug: controller_path = $controller_path -->";

// Vérifier si les fichiers existent
if (!file_exists($config_path)) {
    die("ERREUR: Fichier config.php introuvable à: $config_path");
}

if (!file_exists($controller_path)) {
    die("ERREUR: Fichier ReclamationController.php introuvable à: $controller_path");
}

include_once $config_path;
include_once $controller_path;

$controller = new ReclamationController();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $controller->createReclamation(
                    $_POST['user'],
                    $_POST['sujet'],
                    $_POST['description'],
                    $_POST['statut']
                );
                break;
            
            case 'update':
                $controller->updateReclamation(
                    $_POST['id'],
                    $_POST['user'],
                    $_POST['sujet'],
                    $_POST['description'],
                    $_POST['statut']
                );
                break;
            
            case 'delete':
                $controller->deleteReclamation($_POST['id']);
                break;
            
            case 'update_statut':
                $controller->updateStatut($_POST['id'], $_POST['statut']);
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$reclamations = $controller->getAllReclamations();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Réclamations</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .dashboard-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .stat-card {
            background: rgba(20,24,60,0.9);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            margin: 10px;
            min-width: 200px;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }
        .quick-actions {
            text-align: center;
            margin: 30px 0;
        }
        .btn-dashboard {
            display: inline-block;
            padding: 12px 25px;
            margin: 0 10px;
            background: #6C63FF;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .statut-en-attente {
            background: #FFB347;
            color: #0B0E26;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .statut-confirmé {
            background: #4AFF8B;
            color: #0B0E26;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .statut-rejeté {
            background: #FF6B6B;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>📊 Tableau de Bord</h1>
    <nav>
        <a href="index.php" style="color:#FFB347; font-weight:bold;">Accueil</a>
        <a href="liste.php">Liste</a>&nbsp;
    </nav>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">Bienvenue dans l'administration des réclamations</h2>
    </div>

    <!-- Statistiques -->
    <div class="dashboard-stats">
        <?php
        $total = count($reclamations);
        $en_attente = 0;
        $confirme = 0;
        $rejete = 0;
        
        foreach($reclamations as $reclamation) {
            switch($reclamation['statut']) {
                case 'en attente': $en_attente++; break;
                case 'confirmé': $confirme++; break;
                case 'rejeté': $rejete++; break;
            }
        }
        ?>
        
        <div class="stat-card">
            <h3>Total</h3>
            <div class="stat-number"><?php echo $total; ?></div>
            <p>Réclamations</p>
        </div>
        
        <div class="stat-card">
            <h3>En attente</h3>
            <div class="stat-number" style="color:#FFB347;"><?php echo $en_attente; ?></div>
            <p>À traiter</p>
        </div>
        
        <div class="stat-card">
            <h3>Confirmées</h3>
            <div class="stat-number" style="color:#4AFF8B;"><?php echo $confirme; ?></div>
            <p>Validées</p>
        </div>
        
        <div class="stat-card">
            <h3>Rejetées</h3>
            <div class="stat-number" style="color:#FF6B6B;"><?php echo $rejete; ?></div>
            <p>Refusées</p>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="liste.php" class="btn-dashboard">📋 Voir Toutes les Réclamations</a>
    </div>

    <!-- Dernières réclamations -->
    </main>

<footer>
    <p>Powered by TEC_MAX 2025 © Version 1.0✨</p>
</footer>

</body>
</html>