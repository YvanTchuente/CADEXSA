<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Presentation\Routing\Exceptions\RouteNotFoundException;
use Cadexsa\Presentation\Http\Exceptions\MethodNotAllowedHttpException;

class RouteCollection implements \Countable, \IteratorAggregate
{
    /**
     * An array of the routes keyed by method.
     *
     * @var Route[][]
     */
    private array $routes = [];

    /**
     * A look-up table of routes by their names.
     *
     * @var Route[]
     */
    private array $nameList = [];

    /**
     * All of the routes.
     *
     * @var Route[]
     */
    private array $allRoutes = [];

    /**
     * Add a Route instance to the collection.
     */
    public function add(Route $route): Route
    {
        foreach ($route->methods() as $method) {
            $this->routes[$method][] = $route;
        }

        if ($name = $route->getName()) {
            $this->nameList[$name] = $route;
        }

        $this->allRoutes[] = $route;

        return $route;
    }


    /**
     * Get routes from the collection by a given method.
     * 
     * @return Route[]
     */
    public function get(string $method = null)
    {
        if (is_null($method)) {
            return $this->getRoutes();
        }

        if (isset($this->routes[$method])) {
            return $this->routes[$method];
        }

        return array();
    }

    /**
     * Determine if the route collection contains a given named route.
     * 
     * @return bool
     */
    public function hasNamedRoute($name)
    {
        return !is_null($this->getByName($name));
    }

    /**
     * Get a route instance by its name.
     */
    public function getByName($name): Route|null
    {
        if (isset($this->nameList[$name])) {
            return $this->nameList[$name];
        }

        $routes = array_merge(array_filter($this->allRoutes, function ($route) use ($name) {
            return $route->getName() === $name;
        }));
        if ($routes) {
            $route = $routes[0];
            $this->nameList[$route->getName()] = $route;

            return $route;
        }

        return null;
    }

    /**
     * Get all of the routes in the collection.
     *
     * @return Route[]
     */
    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }

    /**
     * Finds the first route matching a given request.
     *
     * @throws MethodNotAllowedHttpException
     * @throws RouteNotFoundException
     */
    public function match(ServerRequestInterface $request): Route
    {
        $routes = $this->get($request->getMethod());

        $route = $this->matchAgainstRoutes($routes, $request);

        if (!is_null($route)) {
            return $route;
        }

        $other_methods = array_diff(Router::$methods, [$request->getMethod()]);
        $other_routes = array_values(array_filter(
            $other_methods,
            function ($method) use ($request) {
                $routes = $this->get($method);
                return !is_null($this->matchAgainstRoutes($routes, $request));
            }
        ));

        if (count($other_routes) > 0) {
            throw new MethodNotAllowedHttpException(
                $other_methods,
                sprintf(
                    'The %s method is not supported for this route. Supported methods: %s.',
                    $request->getMethod(),
                    implode(', ', $other_routes)
                )
            );
        }

        throw new RouteNotFoundException(
            sprintf("[%s] does not match a route.", $request->getUri())
        );
    }


    /**
     * Get an iterator for the items.
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getRoutes());
    }

    /**
     * Count the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->getRoutes());
    }

    /**
     * Determines if a route in the given array of routes matches the given request.
     *
     * @param Route[] $routes
     */
    private function matchAgainstRoutes(array $routes, ServerRequestInterface $request): Route|null
    {
        foreach ($routes as $route) {
            if ($route->matches($request)) {
                return $route;
            }
        }

        return null;
    }
}
