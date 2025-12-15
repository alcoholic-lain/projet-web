<?php
session_start();
require_once "../../config.php";
require "../../phpmailer/src/PHPMailer.php";
require "../../phpmailer/src/SMTP.php";
require "../../phpmailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

$response = ['errors' => [], 'success' => false];

// 🔹 Vérification session
if (!isset($_SESSION['user_id'])) {
    $response['errors']['general'] = "Non autorisé";
    echo json_encode($response);
    exit;
}

$db = Config::getConnexion();

// 🔹 Récupération des champs
$current = $_POST['currentPassword'] ?? '';
$new     = $_POST['newPassword'] ?? '';
$confirm = $_POST['confirmPassword'] ?? '';

// 🔹 Validation côté serveur
if ($current === '') $response['errors']['currentPassword'] = "Veuillez saisir le mot de passe actuel";

if (strlen($new) < 8) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins 8 caractères";
if (!preg_match('/[A-Z]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins une majuscule";
if (!preg_match('/[a-z]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins une minuscule";
if (!preg_match('/[0-9]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins un chiffre";
if (!preg_match('/[!@#$%^&*()_\-+=<>?]/', $new)) $response['errors']['newPassword'] = "Le mot de passe doit contenir au moins un symbole spécial (!@#$%^&*)";
if ($new !== $confirm) $response['errors']['confirmPassword'] = "Les mots de passe ne correspondent pas";

if (!empty($response['errors'])) {
    echo json_encode($response);
    exit;
}

// 🔹 Vérifier mot de passe actuel
$stmt = $db->prepare("SELECT password, email, pseudo FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($current, $user['password'])) {
    $response['errors']['currentPassword'] = "Mot de passe actuel incorrect";
    echo json_encode($response);
    exit;
}

// 🔹 Vérifier mot de passe différent
if (password_verify($new, $user['password'])) {
    $response['errors']['newPassword'] = "Vous ne pouvez pas réutiliser votre ancien mot de passe";
    echo json_encode($response);
    exit;
}

// 🔹 Update mot de passe
$hash = password_hash($new, PASSWORD_DEFAULT);
$update = $db->prepare("UPDATE user SET password = ? WHERE id = ?");
$update->execute([$hash, $_SESSION['user_id']]);

// 🔹 Enregistrer l'action de modification
$stmtAct = $db->prepare("INSERT INTO user_activity (user_id, action, created_at) VALUES (?, ?, NOW())");
$stmtAct->execute([$_SESSION['user_id'], 'modification de mot de passe']);

// 🔹 Envoi du mail d'alerte
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'zakariabenwirane@gmail.com';   // Ton Gmail
    $mail->Password   = 'sfcw omdt itty uhyt';          // Ton mot de passe application Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('zakariabenwirane@gmail.com', 'Space Admin');
    $mail->addAddress($user['email'], $user['pseudo']);
    $mail->isHTML(true);
    $mail->Subject = '⚠️ Alerte sécurité : mot de passe modifié';
    $mail->Body = "
        <div style='font-family:Arial,sans-serif;background:#0f0f1a;color:white;padding:50px;text-align:center;border-radius:20px;max-width:600px;margin:auto;'>
            <h1 style='color:#8b5cf6;'>Space</h1>
            <h2>Salut {$user['pseudo']} !</h2>
            <p>Ton mot de passe a été récemment modifié.</p>
            <p>Si ce n'était pas toi, contacte immédiatement le support.</p>
        </div>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log("Erreur envoi mail alerte mot de passe : " . $e->getMessage());
}

// 🔹 Réponse succès
$response['success'] = true;
echo json_encode($response);
