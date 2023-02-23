<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Authorization;

use Cadexsa\Infrastructure\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAware;
use Cadexsa\Presentation\Http\HttpMessageFactoriesAwareTrait;
use Cadexsa\Presentation\Authorization\Contracts\Gate as GateContract;

class Gate implements GateContract, HttpMessageFactoriesAware
{
    use HttpMessageFactoriesAwareTrait;

    /**
     * The application instance.
     */
    private Application $application;

    /**
     * The user resolver callable.
     *
     * @var callable
     */
    private $userResolver;

    /**
     * All of the defined rules.
     */
    private array $rules;

    public function __construct(Application $application, callable $userResolver)
    {
        $this->application = $application;
        $this->userResolver = $userResolver;
    }

    public function defineRule(string $route, callable $callback)
    {
        if (!$this->application->getRouter()->getRoutes()->getByName($route)) {
            throw new \RuntimeException("Undefined [$route] route.");
        }

        $this->rules[$route] = $callback;
    }

    public function needsAuthorization(ServerRequestInterface $request)
    {
        if ($route = $this->application->getRouter()->getRoutes()->match($request)) {
            if (isset($this->rules[$route->getName()])) {
                return true;
            }
        }

        return false;
    }

    public function isAuthorized(ServerRequestInterface $request)
    {
        $route = $this->application->getRouter()->getRoutes()->match($request);
        $callback = $this->rules[$route->getName()];

        return call_user_func($callback, ($this->userResolver)());
    }

    public function unauthorized(ServerRequestInterface $request): ResponseInterface
    {
        $url = sprintf("%s?goto=%s", route('login'), rawurlencode($request->getRequestTarget()));
        $body = $this->streamFactory->createStream(
            sprintf(
                '<!DOCTYPE html>
                     <html>
                        <head>
                            <meta charset="UTF-8" />
                            <meta http-equiv="refresh" content="0;url=\'%1$s\'" />
                            <title>Redirecting to %1$s</title>
                        </head>
                     <body>Redirecting to <a href="%1$s">%1$s</a>.</body>
                </html>',
                htmlspecialchars($url, \ENT_QUOTES, 'UTF-8')
            )
        );
        $response = $this->responseFactory->createResponse(403)->withHeader('Location', $url)->withBody($body);

        return $response;
    }
}
