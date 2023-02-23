<?php

namespace Cadexsa\Presentation\Http\Exceptions;

class HttpException extends \RuntimeException
{
    private array $headers;

    /**
     * @param string $message The exception message.
     * @param integer $code The HTTP status code.
     * @param array $headers The HTTP headers
     */
    public function __construct(string $message = "", int $code = 0, \Throwable|null $previous = null, array $headers = [])
    {
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->getCode();
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
