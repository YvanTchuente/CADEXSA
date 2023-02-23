<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cadexsa\Presentation\Authorization\Contracts\Gate;

class AuthorizeRequests implements MiddlewareInterface
{
    private Gate $gate;

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->gate->needsAuthorization($request)) {
            return $handler->handle($request);
        }

        if (!$this->gate->isAuthorized($request)) {

            app()->getLogger()->warning("Unauthorized access to '{resource}' detected from a device at '{address}'", ['resource' => $request->getUri()->getPath(), 'address' => $request->getServerParams()['REMOTE_ADDR']]);

            return $this->gate->unauthorized($request);
        }

        return $handler->handle($request);
    }
}
