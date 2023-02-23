<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Logging;

use Cadexsa\Infrastructure\Application;
use Psr\Log\LoggerInterface;

class LogManager implements LoggerInterface
{
    /**
     * The application instance.
     */
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get a log driver instance.
     *
     * @return LoggerInterface
     */
    public function driver()
    {
        $config = new Configuration(
            storage_path("logs"),
            config('mail.accounts.info'),
            config('mail.accounts.admin')
        );
        
        return new Logger($config, $this->app->getMailer());
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->emergency($message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->alert($message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->critical($message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->error($message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->warning($message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->notice($message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->info($message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->driver()->debug($message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->driver()->log($level,$message, $context);
    }
}
