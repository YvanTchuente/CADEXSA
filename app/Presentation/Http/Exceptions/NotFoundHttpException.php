<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Exceptions;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = "", \Throwable $previous = null, array $headers = [])
    {
        parent::__construct($message, 404, $previous, $headers);
    }
}
