<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Domain\Model\ExStudent\ExStudentLoggedIn;
use Cadexsa\Domain\Model\ExStudent\ExStudentLoggedOut;
use Cadexsa\Domain\Model\ExStudent\ExstudentRegistered;

class ExStudentSubscriber
{
    public function onExstudentRegistered(ExstudentRegistered $event)
    {
        try {
            // Register the ex-student for the newsletter service
            $connection = app()->database->getConnection();
            $connection->pdo->beginTransaction();
            $stmt = $connection->pdo->prepare("INSERT INTO newsletter_subscribers (name, email) VALUES (:name, :email)");
            $stmt->bindValue('name', $event->exstudent->getName());
            $stmt->bindValue('email', $event->exstudent->getEmailAddress());
            $stmt->execute();

            // Generate an activation link
            $link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/exstudents/activate?id=" . $event->exstudent->getId();

            // Send the activation email at the given email address
            $subject = "ExStudent Registration Activation";
            $recipientName = (string) $event->exstudent->getName();
            $params = ['link' => $link, 'recipientName' => $recipientName, 'host' => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']];
            $activation_email_view = new View(views_path('emails/activation_email.php'), $params);
            $activation_email = $activation_email_view->render();

            $mailer = app()->getMailer();
            $mailer->from(config('mail.accounts.members'), "Cadexsa Accounts")
                ->to($event->exstudent->getEmailAddress(), $recipientName)
                ->send($activation_email, $subject);

            $connection->pdo->commit();

            // Log the registration
            app()->getLogger()->info("An ex-student '{name}' requested for membership at {time}", ['name' => (string) $event->exstudent->getName(), 'time' => $event->occurredOn()->format("H:i:s A")]);
        } catch (\Throwable $e) {
            $connection->pdo->rollBack();
            throw $e;
        }
    }

    public function onLogin(ExStudentLoggedIn $event)
    {
        $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('username', $event->username));
        app()->getLogger()->info("'{name}' logged in to their account from a device at the IP address {address} at {time}", ['name' => (string) $exstudent->getName(), 'address' => $_SERVER['REMOTE_ADDR'], 'time' => $event->occurredOn()->format("h:i:s A")]);
    }

    public function onLogout(ExStudentLoggedOut $event)
    {
        $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('id', $event->id));
        app()->getLogger()->info("'{name}' logged out of their account from a device at the IP address {address} at {time}", ['name' => (string) $exstudent->getName(), 'address' => $_SERVER['REMOTE_ADDR'], 'time' => $event->occurredOn()->format("h:i:s A")]);
    }
}
