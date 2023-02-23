<?php

namespace Cadexsa\Presentation\Routing;

use Psr\Http\Message\ServerRequestInterface;

class RouteParameterBinder
{
    /**
     * The route instance.
     */
    private Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Get the parameters for the route.
     */
    public function parameters(ServerRequestInterface $request)
    {
        return $this->bindPathParameters($request);
    }

    /**
     * Get the parameter matches for the path portion of the URI.
     */
    private function bindPathParameters(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();

        $pattern = $this->route->compilePath();
        preg_match($pattern, $path, $matches);

        return $this->matchToKeys(array_slice($matches, 1));
    }

    /**
     * Combine a set of parameter matches with the route's keys.
     */
    private function matchToKeys(array $matches)
    {
        if (empty($parameterNames = $this->route->parameterNames())) {
            return [];
        }

        foreach ($parameterNames as $key => $name) {
            $parameters[$name] = array_intersect_key($matches, $parameterNames)[$key];
        }


        return array_filter($parameters, function ($value) {
            return is_string($value) && strlen($value) > 0;
        });
    }
}
