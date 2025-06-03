<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_POST['correo']) || !isset($_POST['msg'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

$Correo = $_POST['correo'];
$msg = $_POST['msg'];

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'brosstorexz@gmail.com';
    $mail->Password   = 'uqta fymj eozn bsur'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('brosstorexz@gmail.com', 'Tu Tienda Brosstore');
    $mail->CharSet = 'UTF-8';
    $mail->addAddress('brosstorexz@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Correo enviado desde formulario';
    $mail->Body = "
    <html>
    <head> ... </head>
    <body>
        ...
        <h2>Hola, desde {$Correo}</h2>
        <p>{$msg}</p>
        ...
    </body>
    </html>";

    $mail->send();
    http_response_code(200);
    echo json_encode(['success' => 'Correo enviado correctamente']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
}
