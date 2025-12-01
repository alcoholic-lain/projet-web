<?php
require_once __DIR__ . "/../../security.php";
requireAdmin();

require_once $_SERVER['DOCUMENT_ROOT'] . "/projet-web/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/projet-web/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$id = intval($_POST['innovation_id'] ?? 0);
$statut = $_POST['statut'] ?? '';

if (!$id || !in_array($statut, ['Validée', 'Rejetée'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

$db = config::getConnexion();

/* ✅ 1. Mise à jour du statut */
$sql = "UPDATE innovations SET statut = :statut WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([
    ':statut' => $statut,
    ':id' => $id
]);

/* ✅ 2. Récupérer l’email depuis la table `user` */
$sqlUser = "
SELECT u.email, u.pseudo, i.titre
FROM innovations i
JOIN user u ON i.user_id = u.id
WHERE i.id = :id
";

$q = $db->prepare($sqlUser);
$q->execute([':id' => $id]);
$user = $q->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur introuvable']);
    exit;
}

/* ✅ 3. Envoi de l’email */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(SMTP_USER, 'Tunispace');
    $mail->addAddress($user['email'], $user['pseudo']);

    $mail->isHTML(true);

    if ($statut === 'Validée') {
        $mail->Subject = "✅ Innovation validée";
        $mail->Body = "
            Bonjour <b>{$user['pseudo']}</b>,<br><br>
            Votre innovation <b>{$user['titre']}</b> a été <span style='color:green'>VALIDÉE</span> ✅.<br>
            Elle est maintenant visible sur la plateforme Tunispace.<br><br>
            Félicitations 🚀
        ";
    } else {
        $mail->Subject = "❌ Innovation refusée";
        $mail->Body = "
            Bonjour <b>{$user['pseudo']}</b>,<br><br>
            Votre innovation <b>{$user['titre']}</b> a été <span style='color:red'>REFUSÉE</span> ❌.<br>
            Vous pouvez la modifier puis la renvoyer.<br><br>
            Bon courage 💪
        ";
    }

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $mail->ErrorInfo
    ]);
}
