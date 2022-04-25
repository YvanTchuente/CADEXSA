<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\ServerRequest;
use PHPMailer\PHPMailer\PHPMailer;

$incoming = (new ServerRequest())->initialize();

// Retrieve the content of the contact form
$payload = $incoming->getParsedBody();
$firstname = $payload['first-name'];
$lastname = $payload['last-name'];
$email = $payload['email'];
$phone_number = $payload['phoneNumber'];
$message = $payload['message'];

// Process the request
$admin_mail = "yvantchuente@gmail.com";
$from_mail = $email;
$subject = "New message from Contact Form";
$senderName = $firstname . " " . $lastname;

$mail = new PHPMailer(true);

// Mail Recipients
$mail->setFrom($from_mail, $senderName);
$mail->addAddress($admin_mail);     //Add a recipient

// Mail Content
$mail->Subject = $subject;
$mail->Body = $msg;

// Send the mail
$mail->send();

header('Location: /contact_us/');
