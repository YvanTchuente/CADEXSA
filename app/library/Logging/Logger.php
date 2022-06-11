<?php

/**
 * System Logger class
 * 
 * Implements the PSR-3 Logger Interface
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 * @copyright 2022 Yvan Tchuente
 */

declare(strict_types=1);

namespace Application\Logging;

use Application\{
    MailerAware,
    MailerAwareTrait
};
use Psr\Log\{
    LogLevel,
    LoggerInterface,
    InvalidArgumentException
};

/**
 * System Logger
 * 
 * Logs system messages as per their RFC 5424 severity levels to a 
 * centralized location.
 * 
 * The log messages are grouped into batches according to the date
 * of their creation and stored in files.
 * 
 * Each log file is named according to the date stamp of the batch
 * of log messages it contains in the format '*yyyy-mm-dd*'.
 * 
 * **Note**: The `alert` method will raise an exception if it cannot
 *           send the notification email.
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */
class Logger implements LoggerInterface, MailerAware
{
    private const LOG_LEVELS = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLeveL::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY
    ];

    /** 
     * Path to directory housing the logs
     * 
     * @var string
     */
    private $directory;

    /**
     * Email address the script uses to send alert emails
     * 
     * @var string
     */
    private $address;

    /**
     * email address of the administrator
     * 
     * @var string
     */
    private $adminAddress;

    /**
     * Name of administrator
     * 
     * @var string
     */
    private $adminName;

    use MailerAwareTrait;

    /**
     * Initializes the logger
     * 
     * The logger requires three directories namely, **info**, **errors** and **emergencies** to be present
     * in the provided logs directory. If the logs directory is missing these directories,
     * the logger will attempt to create them.
     * 
     * The logs are stored according to their RFC 5424 severity level values. Below are details
     * of the how the logs are stored.
     * 
     * - For informational messages, warnings and notices, their corresponding logs are stored
     *   in the info directory.
     * 
     * - For messages describing critical and error contitions, their corresponding logs are
     *   stored in the errors directory.
     * 
     * - For messages describing alerts and emergencies, their corresponding logs are stored
     *   in the emergencies directory.
     * 
     * @param string $directory The path to the directory that shall keep the logs.
     * 
     * @throws \InvalidArgumentException If the logs directory does not exists or is not a directory.
     */
    public function __construct(string $directory)
    {
        if (!file_exists($directory)) {
            throw new \InvalidArgumentException(sprintf("%s does not exists", $directory));
        }
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf("%s is not a directory", $directory));
        }
        $this->directory = rtrim($directory, '/');
        if (!file_exists($directory . '/info')) {
            mkdir($directory . '/info');
        }
        if (!file_exists($directory . '/errors')) {
            mkdir($directory . '/errors');
        }
        if (!file_exists($directory . '/emergencies')) {
            mkdir($directory . '/emergencies');
        }
    }

    /**
     * Sets mailer configuration settings for sending alert mails
     *
     * @param string $address Email address of the sender of alert mails
     * @param string $adminAddress Email address of the recipient of alert mails
     * @param string $adminName The name of the recipient of alert mails
     * 
     * @return static
     */
    public function setAlertConfigs(string $address, string $adminAddress, string $adminName)
    {
        if (!$address || !$adminAddress || !$adminName) {
            throw new \InvalidArgumentException("Some argument(s) is/are empty");
        }
        switch (true) {
            case (!filter_var($address, FILTER_VALIDATE_EMAIL)):
                throw new \InvalidArgumentException("$address is an invalid email address");
                break;
            case (!filter_var($adminAddress, FILTER_VALIDATE_EMAIL)):
                throw new \InvalidArgumentException("$adminAddress is an invalid email address");
                break;
        }
        $this->address = $address;
        $this->adminAddress = $adminAddress;
        $this->adminName = $adminName;
        return $this;
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] EMERGENCY: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(3), $log_message, FILE_APPEND);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        // Log the alert
        $current_timestamp = date('c');
        $header = "[$current_timestamp] ALERT: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(3), $log_message, FILE_APPEND);
        // Alert the administrator
        if (!isset($this->mailer)) {
            throw new \RuntimeException("There is no configured mailer!");
        }
        if (!$this->address || !$this->adminAddress || !$this->adminName) {
            throw new \RuntimeException("Some alert configuratrion(s) is/are empty");
        }
        $mail_body = $this->prepareAlertMailBody($message, time());
        $this->mailer->setSender($this->address, 'Logger alert script');
        $this->mailer->setRecipient($this->adminAddress, $this->adminName);
        $this->mailer->setBody($mail_body, 'A worse-case scenario has occured', true);
        $has_sent = $this->mailer->send();
        if (!$has_sent) {
            $error_message = $this->mailer->getError();
            throw new \RuntimeException($error_message);
        }
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] CRITICAL CONDITION: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(2), $log_message, FILE_APPEND);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] RUNTIME ERROR: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(2), $log_message, FILE_APPEND);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] WARNING: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(1), $log_message, FILE_APPEND);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] NOTICE: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(1), $log_message, FILE_APPEND);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] INFORMATION: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(1), $log_message, FILE_APPEND);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $current_timestamp = date('c');
        $header = "[$current_timestamp] DEBUG INFORMATION: ";
        $message = $this->interpolate($message, $context);
        $log_message = $header . $message . PHP_EOL . PHP_EOL;
        file_put_contents($this->getLog(1), $log_message, FILE_APPEND);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!in_array($level, self::LOG_LEVELS, true)) {
            throw new InvalidArgumentException("Invalig log level");
        }
        switch ($level) {
            case LogLevel::DEBUG:
                $this->debug($message, $context);
                break;
            case LogLevel::INFO:
                $this->info($message, $context);
                break;
            case LogLevel::NOTICE:
                $this->notice($message, $context);
                break;
            case LogLevel::WARNING:
                $this->warning($message, $context);
                break;
            case LogLevel::ERROR:
                $this->error($message, $context);
                break;
            case LogLevel::CRITICAL:
                $this->critical($message, $context);
                break;
            case LogLevel::ALERT:
                $this->alert($message, $context);
                break;
            case LogLevel::EMERGENCY:
                $this->emergency($message, $context);
                break;
        }
    }

    /**
     * Interpolates context values into the message's placeholders.
     *
     * @param string|\Stringable $message Sytem message
     * @param array $context Context values
     * 
     * @return string
     */
    private function interpolate(string|\Stringable $message, array $context)
    {
        if (!$context) {
            return $message;
        }
        $replace = [];
        if (isset($context['exception']) && $context['exception'] instanceof \Exception) {
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
                $replace['{' . $key . '}'] = $value;
            }
        }
        foreach ($replace as $key => $value) {
            $message = preg_replace('/' . $key . '/', $value, $message);
        }
        return $message;
    }

    /**
     * Returns the filename of the current log file in use
     * 
     * Depending on the nature of the message to log, it returns the path to the current file of the current log file.
     * Possible values of the type parameter include:
     * 
     * - 1: For informational messages, warning and notices.
     * - 2: For messages describing critical and error conditions.
     * - 3: For alert and emergencies.
     * 
     * @param integer|null $type Nature of the message to log.
     * 
     * @return string
     */
    private function getLog(int $type)
    {
        $logFilename = $this->directory;
        switch ($type) {
            case 1:
                $logFilename .= '/info';
                break;
            case 2:
                $logFilename .= '/errors';
                break;
            case 3:
                $logFilename .= '/emergencies';
                break;
        }
        $logFilename .= '/' . date('Y-m-d') . '.log';
        return $logFilename;
    }

    private function prepareAlertMailbody(string $message, int $timestamp)
    {
        $body = "<p>A worse-case scenario has occured at " . date('h:i:s P', $timestamp) . " on " . date('F jS Y', $timestamp) . "</p>" .
            "<p>Below is the detailed log message of the event:</p>" . "<p><b>$message</b></p>" . "<p><b>ACTION MUST BE TAKEN IMMEDIATELY</b></p>";
        return $body;
    }
}
