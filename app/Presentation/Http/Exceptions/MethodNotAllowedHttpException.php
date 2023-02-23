<?php

namespace Cadexsa\Presentation\Http\Exceptions;

use Cadexsa\Presentation\Http\Exceptions\HttpException;

class MethodNotAllowedHttpException extends HttpException
{
    public function __construct(array $allowedMethods, string $message = "", \Throwable|null $previous = null, array $headers = [])
    {
        $headers['Allow'] = strtoupper(implode(', ', $allowedMethods));

        parent::__construct($message, 405, $previous, $headers);
    }
}
