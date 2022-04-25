<?php

declare(strict_types=1);

namespace Application\MiddleWare\Router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Router class
 * 
 * Routes incoming requests to their corresponding registered actions
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */
class Router
{
    /** 
     * @var ServerRequestInterface 
     */
    private $request;

    /** 
     * Two-dimensional array of registered actions indexed by the corresponding route grouped by
     * request methods
     * 
     * @var callable[][]
     */
    private $routes = [];

    /**
     * The default action the router would match and resolve to 
     * for a request if there was no registered route it
     *  
     * @var callable|null 
     */
    private $default_action;

    /**
     * @param ServerRequestInterface $request The server-side request
     * @param callable|null $action The default action in case there is no registered route for the request
     */
    public function __construct(ServerRequestInterface $request, ?callable $action = null)
    {
        $this->request = $request;
        $this->default_action = $action;
    }

    /**
     * Retrieves the registered routes
     *
     * @return callable[][]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Registers the default action to take when no registered route matches the request
     *
     * @param callable $action
     * 
     * @return static
     */
    public function setDefaultAction(callable $action)
    {
        $this->default_action = $action;
        return $this;
    }

    private function register(string $method, string $route, callable $action)
    {
        $method = strtoupper($method);
        $this->routes[$method][$route] = $action;
        return $this;
    }

    /**
     * Registers a route for GET requests
     *
     * @param string $route The route
     * @param callable $action The action to be taken for the route
     * 
     * @return static
     */
    public function get(string $route, callable $action)
    {
        return $this->register('get', $route, $action);
    }

    /**
     * Registers a route for POST requests
     *
     * @param string $route The route
     * @param callable $action The action to be taken for the route
     * 
     * @return static
     */
    public function post(string $route, callable $action)
    {
        return $this->register('post', $route, $action);
    }

    /**
     * Looks a for an action that corresponds to the client-sent request
     * 
     * Searches for a corresponding action to a request in the internally saved list of actions.
     * It returns the callable or an array contaning the corresponding action indexed as 'action'
     * and a second element indexed as 'arguments' which is an array of arguments that the callable may need
     * which were derived from the request or false if no match could be found
     *
     * @return callable|array|false
     */
    public function match()
    {
        if (empty($this->routes)) {
            if ($this->default_action) {
                return $this->default_action;
            } else {
                throw new \RuntimeException("No route was registered");
            }
        }
        $requestMethod = strtoupper($this->request->getMethod());
        $target = $this->request->getRequestTarget();
        $routes = $this->routes[$requestMethod]; // Retrieves the possible routes for the request's method
        $selected_route_action = null;
        foreach ($routes as $route => $action) {
            $pattern = "/^" . str_replace("/", "\/", $route) . "$/";
            if (preg_match($pattern, $target, $matches)) {
                $selected_route_action = $routes[$route];
                if (count($matches) == 1) {
                    unset($matches);
                }
                break;
            }
        }
        if (!isset($selected_route_action) && $this->default_action) {
            $selected_route_action = $this->default_action;
        }
        if (!isset($selected_route_action)) {
            return false;
        }
        if (isset($matches)) {
            return ['action' => $selected_route_action, 'arguments' => $matches];
        }
        return $selected_route_action;
    }

    /**
     * Routes an incoming request to a registered action
     *
     * @throws RouteNotFoundException If the router could not route the request to an action
     */
    public function resolve()
    {
        $match = $this->match();
        if ($match === false) {
            throw new RouteNotFoundException();
        }
        if (is_array($match)) {
            $action = $match['action'];
            $arguments = $match['arguments'];
            return call_user_func($action, $arguments);
        }
        return call_user_func($match);
    }
}
