<?php
// controller/send_report_email.php
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function sendReportEmail($post_id, $reasonText) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nadhemsaidani718@gmail.com';        // YOUR GMAIL
        $mail->Password   = 'jcac pjgp efyz fkyk';  // FROM GOOGLE APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@tunispace.com', 'TUNISPACE');
        $mail->addAddress('nadhemsaidani718@gmail.com');         // YOUR EMAIL

        $mail->isHTML(true);
        $mail->Subject = "TUNISPACE - New Report (Post #$post_id)";
        $mail->Body    = "
            <h2>New Report!</h2>
            <p><strong>Post ID:</strong> $post_id</p>
            <p><strong>Reasons:</strong><br>" . nl2br(htmlspecialchars($reasonText)) . "</p>
            <p><strong>Time:</strong> " . date('d/m/Y H:i') . "</p>
            <hr>
            <p><a href='http://localhost/tunispace/view/admin_dashboard.php#reports'>View Reports</a></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}
?>