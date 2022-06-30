<?php

declare(strict_types=1);

namespace Application\MiddleWare\Authentication;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};

/**
 * Authenticates a server request and produces a response
 * 
 * An authenticator authenticates an HTTP request and returns the results
 * of the authentication in an HTTP response
 */
interface Authenticator
{
    /**
     * Authenticates a request and produces a response
     */
    public function Authenticate(ServerRequestInterface $request): ResponseInterface;
}
