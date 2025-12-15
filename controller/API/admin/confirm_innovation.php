<?php
require_once __DIR__ . "/../../security.php";
requireAdmin();
define('SMTP_USER', 'challakhihichem1@gmail.com');
define('SMTP_PASS', 'tyqxsbdamxbusljt');
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

/* ✅ 2. Récupérer user + innovation */
$sqlUser = "
SELECT u.id AS user_id, u.email, u.pseudo, i.titre
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

/* ✅ 3. Lien dynamique vers SON innovation */
$baseUrl = "http://localhost/projet-web/view/Client/Innovation/src/list_Innovation.php";
$linkInnovation = $baseUrl . "?user=" . $user['user_id'] . "&innovation=" . $id;

/* ✅ 4. Envoi de l’email */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username  = SMTP_USER;
    $mail->Password  = SMTP_PASS;

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom(SMTP_USER, 'Tunispace');
    $mail->addAddress($user['email'], $user['pseudo']);

    $mail->isHTML(true);
    $mail->AltBody = "Connectez-vous à votre compte Tunispace pour consulter votre innovation.";

    $mail->CharSet = 'UTF-8';

    if ($statut === 'Validée') {

        $mail->Subject = "Innovation Validee";

        $mail->Body = '
<html>
<body style="font-family:Arial,sans-serif;font-size:15px;color:#222;">

<p>Bonjour <b>' . htmlspecialchars($user['pseudo']) . '</b>,</p>

<p>
Votre innovation <b>' . htmlspecialchars($user['titre']) . '</b> a été 
<span style="color:green;font-weight:bold;">VALIDÉE</span> ✅.
</p>

<p style="margin:20px 0;">
<a href="' . $linkInnovation . '" 
   style="display:inline-block;
          background:#0b5ed7;
          color:#ffffff;
          padding:12px 25px;
          text-decoration:none;
          border-radius:8px;
          font-weight:bold;">
    👉 Accéder à mon innovation
</a>
</p>

<p>
Elle est maintenant visible sur la plateforme <b>Tunispace</b> 🚀
</p>

<p>Félicitations ✨</p>

</body>
</html>';


    } else {

        $mail->Subject = "Innovation Refusee";

        $mail->Body = '
<html>
<body style="font-family:Arial,sans-serif;font-size:15px;color:#222;">

<p>Bonjour <b>' . htmlspecialchars($user['pseudo']) . '</b>,</p>

<p>
Votre innovation <b>' . htmlspecialchars($user['titre']) . '</b> a été 
<span style="color:red;font-weight:bold;">REFUSÉE</span> ❌.
</p>

<p style="margin:20px 0;">
<a href="' . $linkInnovation . '" 
   style="display:inline-block;
          background:#dc3545;
          color:#ffffff;
          padding:12px 25px;
          text-decoration:none;
          border-radius:8px;
          font-weight:bold;">
    👉 Modifier mon innovation
</a>
</p>

<p>
Vous pouvez la corriger puis la renvoyer.
</p>

<p>Bon courage 💪</p>

</body>
</html>';

    }

    $mail->send();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $mail->ErrorInfo
    ]);
}
