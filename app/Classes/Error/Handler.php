<?php

declare(strict_types=1);

namespace Classes\Error;

/**
 * Application Exception and Error handler
 * 
 * Gracefully handle application exceptions by logging them
 */
class Handler
{
    /** 
     * Path to the log file
     * 
     * @var string
     */
    private $logFile;

    public function __construct(string $logFileDir = null)
    {
        $logFileDir = $logFileDir ?? __DIR__;
        $logFile = date('Y-m-d') . '.log';
        $this->logFile = $logFileDir . '/' . $logFile;
        $this->logFile = str_replace('//', '/', $this->logFile);
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
    }

    /**
     * Exception handler
     *
     * @param \Throwable $e
     */
    public function exceptionHandler(\Throwable $e)
    {
        $message = sprintf(
            '[%s] %s : %s in %s at line %d' . PHP_EOL . 'Stack trace:' . PHP_EOL . '%s' . PHP_EOL,
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        file_put_contents($this->logFile, $message, FILE_APPEND);
    }

    /**
     * Error handler
     * 
     * @param integer $errno Error number
     * @param string $errstr Errror message
     * @param string $errfile File in which the error occurred
     * @param integer $errline Line at which the error occurred
     */
    public function errorHandler(int $errno, string $errstr, string $errfile, int $errline)
    {
        $message = sprintf(
            '[%s] ERROR No %d : %s on %s at line %d' . PHP_EOL,
            date('Y-m-d H:i:s'),
            $errno,
            $errstr,
            $errfile,
            $errline
        );
        file_put_contents($this->logFile, $message, FILE_APPEND);
    }
}
