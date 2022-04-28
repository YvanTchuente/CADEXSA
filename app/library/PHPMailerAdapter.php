<?php

declare(strict_types=1);

namespace Application;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * A PHPMailer adapter class
 */
class PHPMailerAdapter implements MailerInterface
{
    /**
     * PHPMailer instance
     *
     * @var PHPMailer
     */
    private $mailer;

    /**
     * Most recent error message
     *
     * @var string
     */
    private $error = '';

    /**
     * Initializes the PHPMailer instance
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param integer $port 
     */
    public function __construct(
        string $host,
        string $username,
        string $password = '',
        int $port = 465
    ) {
        if (!$host || !$username) {
            throw new \DomainException("Invalid host or username argument");
        }
        $this->mailer = new PHPMailer(false);
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->SMTPAutoTLS = true;
        $this->mailer->Port = $port;
    }

    public function setSender(string $address, string $name)
    {
        $anyInvalidParam = false;
        switch (true) {
            case !$address:
                $this->error = 'Empty address parameter provided at ' . __METHOD__;
                $anyInvalidParam = true;
                break;
            case !$name:
                $this->error = 'Empty name parameter provided at ' . __METHOD__;
                $anyInvalidParam = true;
                break;
        }
        if ($anyInvalidParam) {
            return $anyInvalidParam;
        }
        $has_set = $this->mailer->setFrom($address, $name);
        if (!$has_set) {
            $this->error = 'Invalid address parameter provided at ' . __METHOD__;
        }
        return $has_set;
    }

    public function setRecipient(string $address, string $name = '')
    {
        if (!$address) {
            $this->error = 'Empty address parameter provided at ' . __METHOD__;
            return false;
        }
        $this->mailer->clearAddresses();
        $has_set = $this->mailer->addAddress($address, $name);
        if (!$has_set) {
            $this->error = 'Invalid address parameter provided at ' . __METHOD__;
        }
        return $has_set;
    }

    public function setRecipients(array $recipients)
    {
        $has_set_all = true;
        foreach ($recipients as $recipient) {
            if (is_array($recipient)) {
                if (!array_key_exists('address', $recipient) || !array_key_exists('name', $recipient)) {
                    $this->mailer->clearAddresses();
                    throw new \InvalidArgumentException("A recipient's name or address is missing");
                }
                $name = $recipient['name'];
                $address = $recipient['address'];
            }
            if (is_string($recipient)) {
                $name = '';
                $address = $recipient;
            }
            $has_set = $this->setRecipient($address, $name);
            $has_set_all = $has_set_all && $has_set;
        }
        if (!$has_set_all) {
            $this->mailer->clearAddresses();
        }
        return $has_set_all;
    }

    public function addReplyAddress(string $address, string $name = '')
    {
        if (!$address) {
            $this->error = 'Empty address parameter provided at ' . __METHOD__;
            return false;
        }
        $has_set = $this->mailer->addReplyTo($address, $name);
        if (!$has_set) {
            $this->error = 'Invalid address parameter provided at ' . __METHOD__;
        }
        return $has_set;
    }

    public function setBody(string $body, string $subject = null, bool $isHTML = false)
    {
        $anyInvalidParam = false;
        switch (true) {
            case !$body:
                $this->error = 'Empty body parameter provided in ' . __METHOD__;
                $anyInvalidParam = true;
                break;
            case (!is_null($subject) and !$subject):
                $this->error = 'Empty subject parameter provided in' . __METHOD__;
                $anyInvalidParam = true;
                break;
        }
        if ($anyInvalidParam) {
            return $anyInvalidParam;
        }
        $this->mailer->Body = $body;
        if ($isHTML) {
            $this->mailer->IsHTML();
        }
        if ($subject) {
            $this->mailer->Subject = $subject;
        }
        return true;
    }

    public function send()
    {
        $this->mailer->send();
        if ($this->mailer->isError()) {
            $this->error = $this->mailer->ErrorInfo;
            return false;
        }
        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
