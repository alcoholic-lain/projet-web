<?php
include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../controller/ReclamationController.php';

$controller = new ReclamationController();
$reclamations = $controller->listReclamations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Réclamations</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <h1>📋 Liste des Réclamations</h1>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="liste.php" style="color:#FFB347; font-weight:bold;">Liste</a>&nbsp;
    </nav>
</header>

<main>
    <div class="text-center">
        <h2 style="margin-top:30px;">Gestion des réclamations</h2>
    </div>

    <section>
        <table>
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
                    <td><?php echo htmlspecialchars($reclamation['id']); ?></td>
                    <td><?php echo htmlspecialchars($reclamation['user']); ?></td>
                    <td><?php echo htmlspecialchars($reclamation['sujet']); ?></td>
                    <td><?php echo htmlspecialchars(substr($reclamation['description'], 0, 50)) . '...'; ?></td>
                    <td><?php echo htmlspecialchars($reclamation['date']); ?></td>
                    <td>
                        <span style="padding: 5px 10px; border-radius: 5px; 
                            <?php 
                            if($reclamation['statut'] == 'confirmé') echo 'background: #4AFF8B; color: #0B0E26;';
                            elseif($reclamation['statut'] == 'rejeté') echo 'background: #FF6B6B; color: white;';
                            else echo 'background: #FFB347; color: #0B0E26;';
                            ?>">
                            <?php echo htmlspecialchars($reclamation['statut']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="update.php?id=<?php echo $reclamation['id']; ?>" class="btn-edit">Modifier</a>
                        <a href="delete.php?id=<?php echo $reclamation['id']; ?>" class="btn-delete" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">Supprimer</a>
                        <a href="show.php?id=<?php echo $reclamation['id']; ?>" class="valider">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>Powered by TEC_MAX 2025 © Version 1.0✨</p>
</footer>

</body>
</html>