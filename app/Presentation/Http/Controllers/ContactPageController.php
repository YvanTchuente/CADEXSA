<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Psr\Http\Message\ResponseInterface;
use Cadexsa\Domain\Model\ExStudent\Name;
use Psr\Http\Message\ServerRequestInterface;

class ContactPageController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        if ($method == "POST") {
            $payload = $request->getParsedBody();
            $firstname = $payload['firstname'];
            $lastname = $payload['lastname'];
            $email = filter_var($payload['email'], FILTER_SANITIZE_EMAIL);
            $phoneNumber = $payload['phoneNumber'];
            $message = $payload['message'];

            try {
                // Validate input data
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException("Invalid email address.");
                }
                if (!preg_match('/(\(+\d{3}\s\))?\d{9}/', $phoneNumber)) {
                    throw new \RuntimeException("Invalid phone number.");
                }

                $connection = app()->database->getConnection();
                $connection->pdo->beginTransaction();
                $name = new Name($firstname, $lastname);
                $sentOn = new \DateTime;
                $insert = $connection->pdo->prepare("INSERT INTO contact_messages (name, email_address, phone_number, message, sent_on) VALUES (:name, :email_address, :phone_number, :message, :sent_on)");
                $insert->bindParam('name', $name);
                $insert->bindValue('email_address', $email);
                $insert->bindValue('phone_number', $phoneNumber);
                $insert->bindValue('message', $message);
                $insert->bindValue('sent_on', $sentOn->format('Y-m-d H:i:s'));
                $insert->execute();

                $view_params = ['name' => $name, 'email' => $email, 'phoneNumber' => $phoneNumber, 'message' => $message, 'sentOn' => $sentOn->format('l, j F Y')];
                $contact_form_email_view = new View(views_path("emails/contact_form_email"), $view_params);

                $mailer = app()->getMailer();
                $mailer->from(config("mail.accounts.info"), "Cadexsa Contact Form");
                $mailer->to(config("mail.accounts.admin"));
                $mailer->send($contact_form_email_view->render(), "New CADEXSA Contact Message");
                $view_params['msg'] = "Your request has successfully been saved";
                $connection->pdo->commit();
            } catch (\Throwable $e) {
                if (isset($connection)) $connection->pdo->rollBack();
                $view_params['msg'] = "A error occurred while processing the request.";
            }
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();

        return $this->prepareResponseFromView(new View(views_path("contact_us.php"), $view_params));
    }
}
