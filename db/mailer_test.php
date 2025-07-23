<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar PHPMailer (ajusta si tu estructura cambia)
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

$mail->SMTPDebug = 2; // o 3 para más detalle
$mail->Debugoutput = 'html';

try {
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jfpereacu.est@cosfacali.edu.co'; // Tu correo Gmail
    $mail->Password = 'zfvmrkrzibzdyzom'; // Contraseña de aplicación
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Configuración del mensaje
    $mail->setFrom('jfpereacu.est@cosfacali.edu.co', 'AcuPro Notifier');
    $mail->addAddress('jfpereacu.est@cosfacali.edu.co', 'Pipe'); // Destinatario

    $mail->isHTML(true);
    $mail->Subject = ' ¡Correo de prueba funcionando!';
    $mail->Body    = 'Correo enviado exitosamente al cliente';

    header('correos.php');

    $mail->send();
    echo ' ¡Correo enviado correctamente!';
} catch (Exception $e) {
    echo ' Error al enviar el correo: ', $mail->ErrorInfo;
}
?>
