<?php

declare(strict_types=1);

namespace Application\Membership;

require_once dirname(__DIR__, 2) . '/config/mailserver.php';

use Application\MailerAware;
use Application\MailerInterface;
use Application\MailerAwareTrait;
use Application\Database\Connector;
use Application\Database\ConnectionAware;
use Application\Database\ConnectionTrait;

/**
 * Automates the sending of newsletter emails
 * 
 * Sends newsletter emails to members and registered subscribers
 */
class NewsletterAgent implements ConnectionAware, MailerAware
{
    protected const TABLE = 'newsletter';

    protected $users;

    public function __construct(Connector $connector, MailerInterface $mailer)
    {
        $this->setConnector($connector);
        $this->users = $this->getUsers();
        $this->SetMailer($mailer);
    }

    use ConnectionTrait;

    use MailerAwareTrait;

    protected function getUsers()
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM newsletter");
        $users = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $users;
    }

    /**
     * Broadcast an email
     *
     * Fetches the list of subscribed users and sends to each of them the  newsletter email
     *
     * @param string $body Body of the mail
     * @param string $subject Subject of the mail
     * @param string $senderName The name of the sender
     * 
     * @return bool false if not all of the emails were sent successfully 
     **/
    public function broadcast(string $body, string $subject, string $senderName = 'CADEXSA Newsletter')
    {
        $has_sent_all = true;
        $this->mailer->setSender(MAILSERVER_NEWSLETTER_ACCOUNT, $senderName);
        foreach ($this->users as $user) {
            $this->mailer->setRecipient($user['email'], $user['name']);
            $body = preg_replace('/\$receiver_mail_address\$/', $user['email'], $body);
            $this->mailer->setBody($body, $subject);
            $has_sent = $this->mailer->send();
            $has_sent_all = $has_sent_all && $has_sent;
        }
        return $has_sent_all;
    }
}
