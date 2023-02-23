<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cadexsa\Presentation\Http\Exceptions\TokenMismatchException;

class VerifyCsrfToken implements MiddlewareInterface
{
    /**
     * The URIs that should be excluded from CSRF verification.
     */
    public array $except = [
        '/', '/aboutus'
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (
            $this->isReading($request) ||
            $this->isExempted($request) ||
            $this->tokensMatch($request)
        ) {
            return $handler->handle($request);
        }

        throw new TokenMismatchException("CSRF token mismatch.");
    }

    /**
     * Determines if the HTTP request uses a ‘read’ verb.
     *
     * @return bool
     */
    private function isReading(ServerRequestInterface $request)
    {
        return in_array(strtoupper($request->getMethod()), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determines if the request has a URI that should pass through CSRF verification.
     *
     * @return bool
     */
    private function isExempted(ServerRequestInterface $request)
    {
        foreach ($this->except as $except) {
            $pattern = sprintf("/%s/", preg_quote($except, "/"));
            $uri = (string) $request->getUri();

            if (preg_match($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if the session and input CSRF tokens match.
     *
     * @return bool
     */
    private function tokensMatch(ServerRequestInterface $request)
    {
        $input_token = $this->getTokenFromRequest($request);
        $session_token = $_SESSION['token'] ?? null;

        if (is_null($session_token)) {
            return false;
        }

        unset($_SESSION['token']);
        return hash_equals($session_token, $input_token);
    }

    /**
     * Gets the CSRF token from the request.
     *
     * @return string
     */
    private function getTokenFromRequest(ServerRequestInterface $request)
    {
        $token = $request->getParsedBody()['token'] ?? $request->getHeaderLine('X-CSRF-TOKEN');

        return $token;
    }
}
