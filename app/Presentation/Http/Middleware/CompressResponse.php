<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tym\Http\Message\Compression\Compressor;

/**
 * Compresses the response body.
 */
class CompressResponse implements MiddlewareInterface
{
    private Compressor $compressor;

    public function __construct(Compressor $compressor)
    {
        $this->compressor = $compressor;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $response = $this->compressor->compress($response);

        return $response;
    }
}
