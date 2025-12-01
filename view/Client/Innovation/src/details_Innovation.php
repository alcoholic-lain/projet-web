<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireLogin();
?>

<?php
require_once __DIR__ . "/../../../../controller/components/Innovation/inns_Config.php";

if (!isset($_SESSION['user_id'])) {
    // soit tu bloques, soit tu rediriges
    die("Vous devez être connecté pour commenter.");
}

$user_id = (int) $_SESSION['user_id'];

$innCtrl = new InnovationController();
$catCtrl = new CategoryController();
$commentCtrl = new CommentController();
$voteCtrl = new VoteController();
$voteModel = new VoteModel();   // ✅ MANQUAIT !


$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { die("ID innovation invalide."); }

// Maintenant on peut appeler les commentaires
$comments = $commentCtrl->getComments($id);

if (isset($_POST["add_comment"])) {
    $content = trim($_POST["content"]);
    $files = $_FILES["files"] ?? null;
    $commentCtrl->addComment($id, $user_id, $content, $files);
    header("Location: details_Innovation.php?id=" . $id);
    exit;
}
$innovation = $innCtrl->getInnovation($id);
if (!$innovation) { die("Innovation introuvable."); }

$category = null;
if (!empty($innovation["category_id"])) {
    $category = $catCtrl->getCategory((int)$innovation["category_id"]);
}

// Votes
$stats = $voteModel->getStats($id);
$userVote = $voteModel->getUserVote($id, $user_id);
$score = $stats["upvotes"] - $stats["downvotes"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Innovation</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../assets/css/details_innovation.css">
</head>

<body>

<canvas id="galaxyCanvas"></canvas>
<div class="bg-animation">
    <div class="neural-network" id="neuralNetwork"></div>
    <div class="particles" id="particles"></div>
</div>

<header class="cs-header">
    <div class="cs-container">
        <a href="../../index.php" class="cs-logo">Innovation</a>
        <nav class="cs-nav">
            <a href="../../index.php">Accueil</a>
            <a href="categories.php">Catégories</a>
            <a href="add_Innovation.php">Ajouter une Innovation</a>
            <a href="list_Innovation.php?user=66">Mes innovations</a>
        </nav>
    </div>
</header>

<main class="cs-main">

    <!-- Bloc principal : Vote + Carte -->
    <div class="post-layout">

        <!-- VOTE -->
        <div class="vote-box">
            <div class="vote-btn <?= $userVote === 'up' ? 'active' : '' ?>" data-vote="up">▲</div>
            <div class="vote-score" id="voteScore"><?= $score ?></div>
            <div class="vote-btn <?= $userVote === 'down' ? 'active' : '' ?>" data-vote="down">▼</div>
        </div>

        <!-- CARTE INNOVATION -->
        <div class="post-card">

            <div class="post-category">
                <?= htmlspecialchars($category["nom"] ?? "Sans catégorie") ?>
            </div>

            <h1 class="post-title"><?= htmlspecialchars($innovation["titre"]) ?></h1>

            <div class="post-meta">
                <span>📅 <?= htmlspecialchars($innovation["date_creation"]) ?></span>
                <span class="badge <?= $cls ?>"><?= htmlspecialchars($innovation["statut"]) ?></span>
            </div>

            <p class="post-desc"><?= nl2br(htmlspecialchars($innovation["description"])) ?></p>

            <!-- AFFICHAGE DES PIÈCES JOINTES -->
            <?php if (!empty($innovation["file"])): ?>
                <div class="attachments">
                    <h3>Pièce jointe :</h3>

                    <?php
                    $file = $innovation["file"]; // ex: view/Client/Innovation/uploads/file_xxx.jpg
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $fileUrl = "/projet-web/" . ltrim($file, '/'); // ✅ URL CORRECTE
                    ?>

                    <div class="piece-jointe-actions">

                        <?php if (in_array($ext, ["png","jpg","jpeg","gif","webp"])): ?>
                            <!-- ✅ BOUTON APERÇU IMAGE -->
                            <img src="<?= $fileUrl ?>"
                                 class="attachment-image"
                                 onclick="openModal('<?= $fileUrl ?>','<?= $ext ?>')"
                                 style="cursor:pointer;">
                        <?php else: ?>
                            <!-- ✅ BOUTON APERÇU FICHIER -->
                            <button class="btn-preview"
                                    onclick="openModal('<?= $fileUrl ?>','<?= $ext ?>')">
                                👁 Aperçu
                            </button>
                        <?php endif; ?>

                        <!-- ✅ BOUTON TÉLÉCHARGER -->
                        <a class="btn-download" href="<?= $fileUrl ?>" download>
                            ⬇ Télécharger
                        </a>

                    </div>

                </div>
            <?php endif; ?>


            <a href="list_Innovation.php?categorie=<?= $innovation['category_id'] ?>" class="post-back">
                ← Retour aux innovations
            </a>

        </div>
    </div>


    <!-- COMMENTAIRES EN BAS -->
    <div class="comment-section">

        <h2>Commentaires</h2>

        <!-- FORMULAIRE -->
        <form action="" method="POST" enctype="multipart/form-data" class="comment-form">
            <textarea name="content" placeholder="Ajouter un commentaire..." required></textarea>
            <button type="submit" name="add_comment">Publier</button>
        </form>

        <!-- LISTE DES COMMENTAIRES -->
        <?php foreach ($comments as $c): ?>
            <div class="comment">
                <strong><?= htmlspecialchars($c['pseudo']) ?></strong>
                <span class="date"><?= $c['created_at'] ?></span>
                <p><?= nl2br(htmlspecialchars($c['content'])) ?></p>
            </div>
        <?php endforeach; ?>

    </div>

</main>


<footer class="cs-footer">
    &copy; 2025 - Tunispace Innovation
</footer>

<script src="../assets/js/details_innovation.js"></script>
<!-- MODAL APERÇU FICHIER -->
<div id="fileModal" class="file-modal" onclick="closeModal()">
    <span class="close-modal">&times;</span>
    <div id="modalContent"></div>

    <a id="downloadBtn" href="#" download class="download-btn">
        ⬇ Télécharger le fichier
    </a>
</div>

</body>
</html>
