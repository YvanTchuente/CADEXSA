<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Presentation\Routing\Exceptions\RouteNotFoundException;

class UrlGenerator
{
    /**
     * The route collection.
     */
    private RouteCollection $routes;

    /**
     * The request instance.
     */
    private ServerRequestInterface $request;

    private RouteUrlGenerator $routeUrlGenerator;

    public function __construct(RouteCollection $routes, ServerRequestInterface $request)
    {
        $this->routes = $routes;
        $this->request = $request;
    }

    /**
     * Get the URL to a named route.
     * 
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function route(string $name, array $parameters = [])
    {
        if (!is_null($route = $this->routes->getByName($name))) {
            return $this->toRoute($route, $parameters);
        }

        throw new RouteNotFoundException("Route [{$name}] not defined.");
    }

    /**
     * Get the URL for a given route instance.
     *
     * @throws UrlGenerationException
     */
    public function toRoute(Route $route, array $parameters)
    {
        return $this->routeUrlGenerator()->url($route, $parameters);
    }

    /**
     * Get the Route URL generator instance.
     */
    private function routeUrlGenerator(): RouteUrlGenerator
    {
        if (!$this->routeGenerator) {
            $this->routeUrlGenerator = new RouteUrlGenerator($this, $this->request);
        }

        return $this->routeUrlGenerator;
    }
}
