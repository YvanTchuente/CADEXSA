<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;
use Cadexsa\Infrastructure\Exceptions\Handler;

class HandleExceptions implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);
    }

    public function handleException(\Throwable $exception)
    {
        try {
            $this->getExceptionHandler()->report($exception);
        } catch (\Exception $e) {
            //
        }

        $this->renderHttpResponse($exception);
    }

    public function handleError(int $level, string $message, string $file, int $line)
    {
        $message = sprintf(
            '%s on %s at line %d',
            $message,
            $file,
            $line
        );

        $logger = app()->getLogger();

        switch ($level) {
            case E_ERROR:
            case E_USER_ERROR:
                $logger->error($message);
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $logger->warning($message);
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $logger->notice($message);
                break;
        }
    }

    /**
     * Render an exception as an HTTP response and send it.
     */
    protected function renderHttpResponse(\Throwable $e)
    {
        $response = $this->getExceptionHandler()->render($e);

        app()->send($response);
    }

    /**
     * Get an instance of the exception handler.
     */
    protected function getExceptionHandler(): Handler
    {
        $handler = new Handler;
        Application::setHttpMessageFactories($handler);

        return $handler;
    }
}
