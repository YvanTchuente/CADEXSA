<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Messaging;

use PHPMailer\PHPMailer\PHPMailer;
use Cadexsa\Infrastructure\Contracts\Mailer as MailerInterface;
use Cadexsa\Infrastructure\Messaging\Exceptions\MailerException;

class Mailer implements MailerInterface
{
    /**
     * The mailer instance.
     */
    private PHPMailer $mailer;

    public function __construct()
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->SMTPAuth = true;
        $mailer->Host = config('mail.host');
        $mailer->Port = config('mail.port');
        $mailer->Username = config('mail.username');
        $mailer->Password = config('mail.password');
        $mailer->SMTPAutoTLS = true;
        $this->mailer = $mailer;
    }

    public function from(string $address, string $name = '')
    {
        try {
            $this->mailer->setFrom($address, $name);
        } catch (\Exception $e) {
            throw new MailerException("Invalid email address.");
        }

        return $this;
    }

    public function to(string $address, string $name = '')
    {
        $this->mailer->clearAddresses();

        try {
            $this->mailer->addAddress($address, $name);
        } catch (\Exception $e) {
            throw new MailerException("Invalid email address.");
        }

        return $this;
    }

    public function send(string $message, string $subject = '', bool $isHTML = false): void
    {
        if (!$message) {
            throw new MailerException("The message is empty.");
        }
        $this->mailer->Body = $message;
        if ($subject) {
            $this->mailer->Subject = $subject;
        }
        if ($isHTML) {
            $this->mailer->IsHTML();
        }

        try {
            $this->mailer->send();
        } catch (\Exception $e) {
            throw new MailerException($e->getMessage(), $e->getCode(), $e);
        }

        $this->reset();
    }

    private function reset()
    {
        $this->mailer->From = '';
        $this->mailer->clearAddresses();
        $this->mailer->Body = '';
        $this->mailer->Subject = '';
    }
}
