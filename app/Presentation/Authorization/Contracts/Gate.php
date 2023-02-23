<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Authorization\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Gate
{
    /**
     * Define a new authorization rule.
     *
     * @param string $route The route name
     * @param callable $callback The callback to implement the rule.
     * @return static
     */
    public function defineRule(string $route, callable $callback);

    /**
     * Determines if a request needs authorization to access this route.
     * 
     * @return bool
     */
    public function needsAuthorization(ServerRequestInterface $request);

    /**
     * Determines if a request is authorized.
     * 
     * @return bool
     */
    public function isAuthorized(ServerRequestInterface $request);

    /**
     * Prepares an '**unauthorized**' response according to a given request.
     */
    public function unauthorized(ServerRequestInterface $request): ResponseInterface;
}
