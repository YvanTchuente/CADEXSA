<?php

namespace Cadexsa\Presentation\Routing\Exceptions;

use Cadexsa\Presentation\Routing\Route;

class UrlGenerationException extends \LogicException
{
    /**
     * Create a new exception for missing route parameters.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array  $parameters
     * @return static
     */
    public static function forMissingParameters(Route $route, array $parameters = [])
    {
        $message = sprintf(
            'Missing required parameters for [Route: %s] [URI: %s]',
            $route->getName(),
            $route->path()
        );

        if (count($parameters) > 0) {
            $message .= sprintf(' [Missing parameters: %s]', implode(', ', $parameters));
        }

        $message .= '.';

        return new static($message);
    }
}
