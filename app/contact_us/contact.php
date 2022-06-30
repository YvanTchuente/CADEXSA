<?php

require_once dirname(__DIR__) . '/bootstrap/starter.php';

use Application\Network\Client;
use Application\PHPMailerAdapter;
use Application\Database\Connection;
use Application\MiddleWare\Http\Message\Factory;

$incoming_request = Factory::createServerRequestFromGlobals();

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

    $client = new Client(Factory::instance(), Factory::instance(), Factory::instance());
    $body = Factory::instance()->createStream(http_build_query($data));
    $request = Factory::instance()->createRequest('post', $url)
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withBody($body);
    $response = $client->sendRequest($request);
    $mail_message = (string) $response->getBody();

    return $mail_message;
}
