<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Routing;

use Cadexsa\Infrastructure\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Presentation\Http\Controllers\Controller;

class Router
{
    /**
     * The route collection instance.
     */
    private RouteCollection $routes;

    /**
     * The fallback handler instance.
     */
    private string $fallback;

    /**
     * The route group attribute stack.
     *
     * @var string[][]
     */
    protected array $groupStack = [];

    /**
     * All of the methods supported by the router.
     *
     * @var string[]
     */
    public static array $methods = ['HEAD', 'GET',  'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];

    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    /**
     * Registers a new GET route.
     */
    public function get(string $path, string $handler): Route
    {
        return $this->addRoute(['GET', 'HEAD'], $path, $handler);
    }

    /**
     * Registers a new POST route.
     */
    public function post(string $path, string $handler): Route
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Registers a new PUT route.
     */
    public function put(string $path, string $handler): Route
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Registers a new DELETE route.
     */
    public function delete(string $path, string $handler): Route
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Registers a new PATCH route.
     */
    public function patch(string $path, string $handler): Route
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    /**
     * Registers a new OPTIONS route.
     */
    public function options(string $path, string $handler): Route
    {
        return $this->addRoute('OPTION', $path, $handler);
    }

    /**
     * Registers a new route with the given methods.
     */
    public function match(array|string $methods, string $path, string $handler): Route
    {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $path, $handler);
    }

    /**
     * Create a route group with shared attributes.
     */
    public function group(array $attributes, \Closure|string $routes)
    {
        $this->updateGroupStack($attributes);

        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Set the fallback handler.
     */
    public function fallback(string $handler)
    {
        if (!is_subclass_of($handler, Controller::class)) {
            throw new \LogicException("$handler is not a valid handler.");
        }
        return $this->fallback = $handler;
    }

    /**
     * Adds a route to the route collection.
     */
    public function addRoute(array|string $methods, string $path, string $handler): Route
    {
        return $this->routes->add($this->createRoute($methods, $path, $handler));
    }

    /**
     * Check if a route with the given name exists.
     * 
     * @return bool
     */
    public function has(string|array $name)
    {
        $names = (array) $name;

        foreach ($names as $name) {
            if (!$this->routes->hasNamedRoute($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets the route collection.
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set the route collection instance.
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Dispatch the request to a route and return the response.
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->runRoute($request, $this->findRoute($request));
        } catch (\Exception $e) {
            if (isset($this->fallback)) {
                $fallback = $this->fallback;
                Application::setHttpMessageFactories($fallback = new $fallback());

                return $fallback->handle($request);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Update the group stack with the given attributes.
     */
    private function updateGroupStack(array $attributes)
    {
        if ($this->groupStack) {
            $attributes = array_merge($this->groupStack, $attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Load the provided routes.
     */
    private function loadRoutes(\Closure|string $routes)
    {
        if ($routes instanceof \Closure) {
            $routes($this);
        } else {
            (new RouteFileRegistrar($this))->register($routes);
        }
    }

    /**
     * Create a Route instance.
     */
    private function createRoute(array|string $methods, string $path, string $handler): Route
    {
        $route = new Route($methods, $path, $handler);

        if ($this->groupStack) {
            $this->setRouteAttributes($route);
        }

        return $route;
    }

    /**
     * Sets the attributes of a Route instance.
     */
    private function setRouteAttributes(Route $route)
    {
        $attributes = array_merge($route->getAttributes(), end($this->groupStack));

        $route->setAttributes($attributes);
    }

    /**
     * Finds the route matching a given request.
     */
    private function findRoute(ServerRequestInterface $request): Route
    {
        return $this->routes->match($request);
    }

    /**
     * Return the response for the given route.
     */
    private function runRoute(ServerRequestInterface $request, Route $route): ResponseInterface
    {
        Application::setHttpMessageFactories($route);

        return $route->run($request);
    }

    /**
     * Run the given route within the middleware instance.
     */
    protected function runRouteWithinStack(Route $route, ServerRequestInterface $request)
    {
        $middleware = $this->getRouteMiddleware($route);

        if ($middleware) {
            $middleware = array_shift($middleware);
            $response = $middleware->process($request, $this);
        }

        return $response;
    }

    /**
     * Gather the middleware for the given route with resolved class names.
     * 
     * @return array
     */
    public function getRouteMiddleware(Route $route)
    {
        $middleware = $route->getMiddleware();

        return $middleware;
    }
}
