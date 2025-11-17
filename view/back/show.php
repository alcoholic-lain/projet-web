<?php
include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../controller/ReclamationController.php';

$id = $_GET['id'] ?? 0;
$controller = new ReclamationController();
$reclamation = $controller->getReclamation($id);

if(!$reclamation) {
    die("Réclamation non trouvée");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Réclamation #<?php echo $id; ?></title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <h1>👁️ Détails Réclamation #<?php echo $id; ?></h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="liste.php">Liste</a>
        <a href="add.php">Ajouter</a>
    </nav>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">Détails de la réclamation</h2>
    </div>

    <section style="width: 60%; margin: 0 auto; background: rgba(20,24,60,0.9); padding: 30px; border-radius: 10px;">
        <div style="margin-bottom: 20px;">
            <strong>ID:</strong> <?php echo htmlspecialchars($reclamation['id']); ?>
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>Utilisateur:</strong> <?php echo htmlspecialchars($reclamation['user']); ?>
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>Sujet:</strong> <?php echo htmlspecialchars($reclamation['sujet']); ?>
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>Description:</strong><br>
            <?php echo nl2br(htmlspecialchars($reclamation['description'])); ?>
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>Date:</strong> <?php echo htmlspecialchars($reclamation['date']); ?>
        </div>
        
        <div style="margin-bottom: 20px;">
            <strong>Statut:</strong> 
            <span style="padding: 5px 10px; border-radius: 5px; 
                <?php 
                if($reclamation['statut'] == 'confirmé') echo 'background: #4AFF8B; color: #0B0E26;';
                elseif($reclamation['statut'] == 'rejeté') echo 'background: #FF6B6B; color: white;';
                else echo 'background: #FFB347; color: #0B0E26;';
                ?>">
                <?php echo htmlspecialchars($reclamation['statut']); ?>
            </span>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="update.php?id=<?php echo $id; ?>" class="btn-edit">Modifier</a>
            <a href="delete.php?id=<?php echo $id; ?>" class="btn-delete" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">Supprimer</a>
            <a href="liste.php" class="valider">Retour à la liste</a>
        </div>
    </section>
</main>

<footer>
    <p>Powered by TEC_MAX 2025 © Version 1.0✨</p>
</footer>

</body>
</html>