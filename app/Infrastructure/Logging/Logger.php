<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Logging;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;
use Cadexsa\Infrastructure\Contracts\Mailer;

/**
 * Logs messages as per the RFC 5424 severity levels to a centralized
 * location.
 * 
 * The log messages are grouped into batches according to the date of
 * their creation and stored in files. Log files are named according
 * to datestamp of their creation.
 */
class Logger implements LoggerInterface
{
    /**
     * The configuration settings.
     */
    private Configuration $config;

    /**
     * The mailer instance.
     */
    private Mailer $mailer;

    /**
     * Initializes the logger.
     * 
     * The logger requires that three directories namely, **info**, **errors** and **emergencies**
     * to be present in the provided logs logs. If this logs is missing these directories,
     * the logger will attempt to create them.
     * 
     * @param Configuration $configuration Logger configuration settings.
     */
    public function __construct(Configuration $config, Mailer $mailer)
    {
        $this->config = $config;
        $this->mailer = $mailer;
    }

    /**
     * Sets the logger's configuration.
     */
    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        // Log the alert
        $this->writeLog(__FUNCTION__, $message, $context);

        // Alert the administrator
        $alert = $this->formatAlert($message);
        $this->mailer
            ->from($this->config->getEmail(), 'Logger alert script')
            ->to($this->config->getTo())
            ->send($alert, 'A worse-case scenario has occurred', true);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $levels = (new \ReflectionClass(LogLevel::class))->getConstants();
        if (!in_array($level, $levels)) {
            throw new InvalidArgumentException("Invalid log level.");
        }
        $this->writeLog($level, $message, $context);
    }

    private function writeLog($level, string|\Stringable $message, array $context)
    {
        $timestamp = date("Y-m-d H:i:s P");
        $message = $this->interpolate($message, $context);
        $log = sprintf("[%s] %s: %s" . PHP_EOL . PHP_EOL, $timestamp, strtoupper($level), $message);
        $file = $this->config->getLogs() . DIRECTORY_SEPARATOR . date('Y-m-d') . ".log";
        file_put_contents($file, $log, FILE_APPEND);
    }

    /**
     * Interpolates context values into the message's placeholders.
     *
     * @param string|\Stringable $message Sytem message
     * @param array $context Context values
     */
    private function interpolate(string|\Stringable $message, array $context)
    {
        if (!$context) {
            return $message;
        }

        $replace = [];
        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $e = $context['exception'];
            $exception_message = sprintf(
                '%s in %s at line %d' . PHP_EOL . 'Stack trace:' . PHP_EOL . '%s',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
            $replace['{exception}'] = $exception_message;
            unset($context['exception']);
        }

        foreach ($context as $key => $value) {
            if (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $replace[sprintf("{%s}", $key)] = $value;
            }
        }

        if (!$replace) {
            return $message;
        }

        foreach ($replace as $key => $value) {
            $message = preg_replace(sprintf("/%s/", $key), $value, $message);
        }

        return $message;
    }

    private function formatAlert(string $message)
    {
        $timestamp = new \DateTime();
        $body = sprintf("<p>A worse-case scenario has occurred at %s on %s</p><p>Below is the detailed log message of the event:</p><p><b>%s</b></p><p><b>AN ACTION MUST BE TAKEN IMMEDIATELY</b></p>", $timestamp->format('H:i:s P'), $timestamp->format('F jS Y'), $message);
        return $body;
    }
}
