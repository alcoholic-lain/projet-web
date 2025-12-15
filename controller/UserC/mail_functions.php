<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../phpmailer/src/PHPMailer.php';
require '../../phpmailer/src/SMTP.php';
require '../../phpmailer/src/Exception.php';

function sendPasswordChangeAlert($email, $pseudo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'zakariabenwirane@gmail.com';  // Remplace par ton email
        $mail->Password   = 'sfcw omdt itty uhyt';       // Mot de passe application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('zakariabenwirane@gmail.com', 'Space Admin');
        $mail->addAddress($email, $pseudo);

        $mail->isHTML(true);
        $mail->Subject = '⚠️ Alerte de sécurité : Changement de mot de passe';
        $mail->Body = "
            <p>Bonjour <strong>{$pseudo}</strong>,</p>
            <p>Votre mot de passe a été récemment modifié.</p>
            <p>Si ce n'était pas vous, contactez immédiatement le support.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Erreur envoi mail alerte mot de passe à {$email} : " . $mail->ErrorInfo);
    }
}
?>





