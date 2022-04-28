<?php

declare(strict_types=1);

namespace Application;

trait MailerAwareTrait
{
    /**
     * The mailer instance
     * 
     * @var MailerInterface|null
     */
    protected ?MailerInterface $mailer;

    public function SetMailer(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
}
