<?php

namespace Cadexsa\Infrastructure\Facades;

/**
 * @method static \Cadexsa\Presentation\Routing\Route any(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route delete(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route fallback(array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route get(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\RouteCollection getRoutes()
 * @method static \Cadexsa\Presentation\Routing\Route match(array|string $methods, string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route options(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route patch(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route post(string $uri, array|string|callable|null $action = null)
 * @method static \Cadexsa\Presentation\Routing\Route put(string $uri, array|string|callable|null $action = null)
 *
 * @see \Cadexsa\Presentation\Routing\Router
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'getRouter';
    }
}
