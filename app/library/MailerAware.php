<?php

declare(strict_types=1);

namespace Application;

/**
 * Describes a mailer-aware instance
 */
interface MailerAware
{
    /**
     * Sets a mailer instance on the object
     *
     * @param MailerInterface $mailer The mailer instance
     */
    public function SetMailer(MailerInterface $mailer);
}
