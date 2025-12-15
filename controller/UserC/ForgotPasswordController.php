<?php
require_once "../../config.php";
require "../../phpmailer/src/Exception.php";
require "../../phpmailer/src/PHPMailer.php";
require "../../phpmailer/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Entre un email valide']);
    exit;
}

try {
    $db = config::getConnexion();

    // Vérifie si l'email existe
    $stmt = $db->prepare("SELECT pseudo FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Même message pour sécurité
        echo json_encode(['success' => true, 'message' => 'Si cet email existe, un lien t’a été envoyé.']);
        exit;
    }

    // Génère le token
    $token = bin2hex(random_bytes(32));
    $link = "http://localhost/prj_webb_integ/view/LoginC/resetpassword.php?token=$token";

    // Sauvegarde le token
    $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())
                  ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()")
        ->execute([$email, $token, $token]);

    // ENVOI DU MAIL (version 2025 XAMPP compatible)
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'zakariabenwirane@gmail.com';                    // Ton Gmail
    $mail->Password   = 'sfcw omdt itty uhyt';                            // TON VRAI MOT DE PASSE D'APPLICATION ICI (16 caractères avec espaces)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // SOLUTION MIRACLE POUR XAMPP (désactive la vérification SSL cassée)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('zakariabenwirane@gmail.com', 'Space');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialise ton mot de passe • Space';

    $mail->Body = "
        <div style='font-family:Arial,sans-serif;background:#0f0f1a;color:white;padding:50px;text-align:center;border-radius:20px;max-width:600px;margin:auto;'>
            <h1 style='color:#8b5cf6;text-align:center;'>Space</h1>
            <h2 style='text-align:center;'>Salut {$user['pseudo']} !</h2>
            <p style='font-size:16px;text-align:center;'>Quelqu’un a demandé à réinitialiser ton mot de passe.</p>
            <div style='text-align:center;margin:40px 0;'>
                <a href='$link' style='background:#8b5cf6;color:white;padding:18px 50px;text-decoration:none;border-radius:15px;font-weight:bold;font-size:18px;'>
                    Changer mon mot de passe
                </a>
            </div>
            <p style='color:#888;font-size:14px;text-align:center;'>Lien valable 1 heure<br>Ignore ce mail si ce n’était pas toi</p>
        </div>
    ";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Lien envoyé ! Vérifie ta boîte mail (et les spams)']);

} catch (Exception $e) {
    // En cas de problème, on voit l'erreur exacte
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>


