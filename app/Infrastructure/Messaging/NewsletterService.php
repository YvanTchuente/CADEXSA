<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Messaging;


class NewsletterService
{
    /**
     * @throws \RuntimeException
     */
    public function broadcastNewsletter(string $body, string $subject, string $sender = 'CADEXSA Newsletter')
    {
        $rs = app()->database->getConnection()->pdo->query("SELECT * FROM newsletter_subscribers");
        $subscribers = $rs->fetchAll(\PDO::FETCH_ASSOC);
        $mailer = new Mailer;
        $mailer->from(config('mail.accounts.newsletter'), $sender);
        try {
            foreach ($subscribers as $subscriber) {
                $mailer->to($subscriber['email'], $subscriber['name']);
                $body = preg_replace('/\$receiver_mail_address\$/', $subscriber['email'], $body);
                $mailer->send($body, $subject);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException("An error occurred while broadcasting the newsletter.");
        }
    }
}
