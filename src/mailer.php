<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'maildev';
    $mail->Port       = 1025;
    $mail->SMTPAuth   = false;
    
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('test@example.com');
    $mail->Subject = 'Test via Maildev';
    $mail->Body    = 'Ceci est un test d\'email local via Maildev.';

    $mail->send();
    echo 'Email envoyé !';
} catch (Exception $e) {
    echo "Erreur lors de l'envoi : {$mail->ErrorInfo}";
}
?>