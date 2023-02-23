<?php

namespace Illuminate\Http\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpResponseException extends \RuntimeException
{
    /**
     * The underlying response instance.
     */
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying response instance.
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
