<?php

namespace Cadexsa\Presentation\Routing\Matching;

use Cadexsa\Presentation\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

class UriValidator implements Validator
{
    public function matches(Route $route, ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $uriRegex = $route->compilePath();

        return preg_match($uriRegex, rawurldecode($path));
    }
}
