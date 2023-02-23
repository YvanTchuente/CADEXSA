<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Logging;

/**
 * Represents a logger configuration.
 */
class Configuration
{
    /**
     * The path to directory that houses the logs.
     */
    private string $logs;

    /**
     * The email address of the logger.
     */
    private string $email;

    /**
     * The email address to send alert mails to.
     */
    private string $to;

    /**
     * @param string $logs The directory that houses the logs.
     * @param string $email The email address of the logger.
     * @param string $to The email address to send alert mails to.
     */
    public function __construct(string $logs, string $email, string $to)
    {
        if (!is_dir($logs)) {
            throw new \LogicException("$logs does not exists !");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \LogicException("$email is not a valid email address.");
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \LogicException("$to is not a valid email address.");
        }

        $this->logs = $logs;
        $this->email = $email;
        $this->to = $to;
    }

    /**
     * Get the path to directory that houses the logs.
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Set the directory that houses the logs.
     *
     * @param string $logs The path to the directory.
     * @return  self
     */
    public function setLogs($logs)
    {
        if (!is_dir($logs)) {
            throw new \LogicException("$logs does not exists !");
        }
        $this->logs = $logs;

        return $this;
    }

    /**
     * Get the email address of the logger.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email address of the logger.
     *
     * @return  self
     */
    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \LogicException("$email is not a valid email address.");
        }
        $this->email = $email;

        return $this;
    }

    /**
     * Get the email address to send alert mails to.
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the email address to send alert mails to.
     *
     * @return  self
     */
    public function setTo($to)
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new \LogicException("$to is not a valid email address.");
        }
        $this->to = $to;

        return $this;
    }
}
