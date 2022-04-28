<?php

require_once dirname(__DIR__) . '/config/index.php';
require_once dirname(__DIR__) . '/config/mailserver.php';

use Application\Network\Requests;
use Application\PHPMailerAdapter;
use Application\Database\Connection;
use Application\MiddleWare\ServerRequest;

$incoming_request =  (new ServerRequest())->initialize();

// Retrieve the content of the contact form
$payload = $incoming_request->getParsedBody();
$firstname = $payload['first-name'];
$lastname = $payload['last-name'];
$senderMail = $payload['email'];
$phone = $payload['phoneNumber'];
$message = $payload['message'];

// Process the request
$admin_mail = MAILSERVER_ADMIN_ACCOUNT;
$subject = "New CADEXSA Contact Message";
$senderName = $firstname . " " . $lastname;

$inserted_id = NULL;
$when = date('Y-m-d H:i:s');
$insert_sql = "INSERT INTO contact_page_messages (name, email, phone, message, timestamp) VALUES (:name, :email, :phone, :message, '$when')";

$stmt = (Connection::Instance())->getConnection()->prepare($insert_sql);
$has_inserted = $stmt->execute(['name' => $senderMail, 'email' => $senderMail, 'phone' => $phone, 'message' => $message]);

if ($has_inserted) {
    $inserted_id = ((Connection::Instance())->getConnection())->lastInsertId();
    $mailer = new PHPMailerAdapter(MAILSERVER_HOST, MAILSERVER_INFO_ACCOUNT, MAILSERVER_PASSWORD);
    $mailer->setSender(MAILSERVER_INFO_ACCOUNT, 'Cadexsa Contact Form');
    $mailer->setRecipient($admin_mail);
    $mailer->setBody(format_mail($message, $senderName, $senderMail, $phone), $subject);
    $has_sent = $mailer->send();    // Send the mail

    if ($has_sent) {
        header('Location: /contact_us/?success');
    } else {
        $delete_sql = "DELETE FROM contact_page_messages WHERE timestamp = '$inserted_id'";
        ((Connection::Instance())->getConnection())->query($delete_sql);
        header('Location: /contact_us/?error');
    }
} else {
    header('Location: /contact_us/?error');
}

function format_mail($message, $senderName, $senderMail, $phone)
{
    global $when;
    $url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/includes/mail_templates/contact_form_mail.php";
    $data = ['name' => $senderName, 'email' => $senderMail, 'phone' => $phone, 'message' => $message, 'when' => date('l, j F Y', strtotime($when))];
    $mail_message = (new Requests())->post($url, $data);
    return $mail_message;
}
