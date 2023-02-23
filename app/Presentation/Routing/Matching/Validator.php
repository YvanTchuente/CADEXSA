<?php

namespace Cadexsa\Presentation\Routing\Matching;

use Cadexsa\Presentation\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

interface Validator
{
    /**
     * Validate a given rule against a route and request.
     *
     * @return bool
     */
    public function matches(Route $route, ServerRequestInterface $request);
}
