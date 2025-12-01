<?php
require_once __DIR__ . "/../../../../controller/security.php";
requireAdmin();
?>

<?php
require_once __DIR__ . "/../../../../config.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/projet-web/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../../../../controller/components/Innovation/CategoryController.php";
require_once __DIR__ . "/../../../../controller/components/Innovation/InnovationController.php";
require_once __DIR__ . "/../../../../model/Innovation/Category.php";
require_once __DIR__ . "/../../../../model/Innovation/Innovation.php";

$catCtrl = new CategoryController();
$innCtrl = new InnovationController();

// Récupérer ID
$id = intval($_GET["id"] ?? 0);

// Récupération innovation existante
$data = $innCtrl->getInnovation($id);

if (!$data) {
    die("⚠️ Innovation introuvable.");
}

// Récupération catégories
$categories = $catCtrl->listCategories();

$error = null;

// Soumission formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);
    $categorie = intval($_POST["category_id"]);
    $statut = trim($_POST["statut"]);
    $user_id = $data["user_id"];

    if ($titre === "" || $description === "" || $categorie <= 0) {
        $error = "⚠️ Tous les champs sont obligatoires.";
    } else {

        $innovation = new Innovation(
                $id,
                $titre,
                $description,
                $categorie,
                $user_id,
                $statut,
                $data["date_creation"]
        );

        try {
            // ✅ Ancien et nouveau statut
            $oldStatut = trim($data["statut"]);
            $newStatut = trim($statut);

            // ✅ Update Base
            $innCtrl->updateInnovation($innovation);

            // ✅ Envoi mail si changement de statut
            if (strcasecmp($oldStatut, $newStatut) !== 0) {

                file_put_contents(
                        "C:/xampp/htdocs/projet-web/test_edit_mail.txt",
                        "EMAIL TRY\n",
                        FILE_APPEND
                );

                $db = config::getConnexion();
                $sql = "
                    SELECT u.email, u.pseudo, i.titre
                    FROM innovations i
                    JOIN user u ON i.user_id = u.id
                    WHERE i.id = :id
                ";
                $stmt = $db->prepare($sql);
                $stmt->execute([':id' => $id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {

                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = SMTP_USER;
                        $mail->Password   = SMTP_PASS;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom(SMTP_USER, 'Tunispace');
                        $mail->addAddress($user['email'], $user['pseudo']);
                        $mail->isHTML(true);

                        if ($newStatut === 'Validée') {
                            $mail->Subject = "✅ Innovation validée";
                            $mail->Body = "
                                Bonjour <b>{$user['pseudo']}</b>,<br><br>
                                Votre innovation <b>{$user['titre']}</b> a été
                                <b style='color:green'>VALIDÉE</b> ✅.<br>
                                Félicitations 🚀
                            ";
                        } elseif ($newStatut === 'Rejetée') {
                            $mail->Subject = "❌ Innovation refusée";
                            $mail->Body = "
                                Bonjour <b>{$user['pseudo']}</b>,<br><br>
                                Votre innovation <b>{$user['titre']}</b> a été
                                <b style='color:red'>REFUSÉE</b> ❌.<br>
                                Vous pouvez la modifier et la renvoyer.
                            ";
                        }

                        $mail->send();

                    } catch (Exception $e) {
                        error_log("Erreur email edit_Innovation : " . $mail->ErrorInfo);
                    }
                }
            }

            // ✅ REDIRECTION FINALE OBLIGATOIRE
            header("Location: a_Innovation.php?msg=updated");
            exit;

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Innovation</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- CSS SPÉCIFIQUE -->
    <link rel="stylesheet" href="../assets/css/edit_Innovation.css">

</head>

<body class="admin-dashboard with-sidebar">

<!-- SIDEBAR GLOBAL -->
<?php include __DIR__ . "/../../layout/sidebar.php"; ?>

<!-- HEADER GLOBAL -->
<?php include __DIR__ . "/../../layout/header.php"; ?>

<main>
    <div class="dashboard-inner">

        <div class="page-header-row">
            <h2 class="section-title-main">✏️ Modifier l’Innovation</h2>
            <a href="a_Innovation.php" class="btn-add">⬅ Retour</a>
        </div>

        <?php if ($error): ?>
            <p class="error">❌ <?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="section-box">

                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre"
                       value="<?= htmlspecialchars($data['titre']) ?>" >

                <label>Description</label>
                <textarea id="description" name="description" ><?=
                    htmlspecialchars($data['description']) ?></textarea>

                <label>Catégorie</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                <?= ($cat['id'] == $data['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <label for="statut">Statut</label>
                <select name="statut">
                    <option value="En attente" <?= $data["statut"]=="En attente"?"selected":"" ?>>En attente</option>
                    <option value="Validée"   <?= $data["statut"]=="Validée"?"selected":"" ?>>Validée</option>
                    <option value="Rejetée"   <?= $data["statut"]=="Rejetée"?"selected":"" ?>>Rejetée</option>
                </select>

                <button class="btn-submit">Mettre à jour</button>

            </div>
        </form>

    </div>
</main>

<footer>
    <p>&copy; 2025 - Innovation - Hichem Challakhi</p>
</footer>

<!-- JS GLOBAL -->
<script src="../../assets/js/admin.js"></script>

<!-- JS SPÉCIFIQUE -->
<script src="../assets/js/edit_Innovation.js"></script>

</body>
</html>
