<?php

namespace Cadexsa\Presentation\Routing;

use Cadexsa\Presentation\Routing\Exceptions\UrlGenerationException;
use Psr\Http\Message\ServerRequestInterface;

class RouteUrlGenerator
{
    /**
     * The URL generator instance.
     */
    private UrlGenerator $urlGenerator;

    /**
     * The request instance.
     */
    private ServerRequestInterface $request;

    public function __construct(UrlGenerator $urlGenerator, ServerRequestInterface $request)
    {
        $this->urlGenerator = $urlGenerator;
        $this->request = $request;
    }

    /**
     * Generates a URL for the given route.
     * 
     * @throws UrlGenerationException
     */
    public function url(Route $route, array $parameters = [])
    {
        $domain = env('APP_URL');
        $path = '/' . trim($this->replaceRouteParameters($route->path(), $parameters), '/');

        $uri = trim($domain . $path, '/');

        if (preg_match_all('/{(.*?)}/', $uri, $matchedMissingParameters)) {
            throw UrlGenerationException::forMissingParameters($route, $matchedMissingParameters[1]);
        }

        return $uri;
    }

    /**
     * Replace all of the wildcard parameters for a route path.
     */
    private function replaceRouteParameters(string $path, array &$parameters)
    {
        $path = $this->replaceNamedParameters($path, $parameters);

        $path = preg_replace_callback('/\{.*?\}/', function ($match) use (&$parameters) {
            // Reset only the numeric keys...
            $parameters = array_merge($parameters);

            if (!isset($parameters[0]) && !preg_match('/\?}$/', $match[0])) {
                $replacement = $match[0];
            } else {
                $replacement = $parameters[0];
                unset($parameters[0]);
            }

            return $replacement;
        }, $path);

        return trim(preg_replace('/\{.*?\?\}/', '', $path), '/');
    }

    /**
     * Replace all of the named parameters in the path.
     */
    private function replaceNamedParameters(string $path, array &$parameters)
    {
        return preg_replace_callback('/\{(.*?)(\?)?\}/', function ($matches) use (&$parameters) {
            if (isset($parameters[$matches[1]]) && $parameters[$matches[1]] !== '') {
                $replacement = $parameters[$matches[1]];
                unset($parameters[$matches[1]]);
                return $replacement;
            } elseif (isset($parameters[$matches[1]])) {
                unset($parameters[$matches[1]]);
            }

            return $matches[0];
        }, $path);
    }
}
