<?php

namespace Cadexsa\Presentation\Routing\Matching;

use Cadexsa\Presentation\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

class MethodValidator implements Validator
{
    public function matches(Route $route, ServerRequestInterface $request)
    {
        return in_array(strtoupper($request->getMethod()), $route->methods());
    }
}
