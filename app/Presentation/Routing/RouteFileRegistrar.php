<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Routing;

use Cadexsa\Presentation\Routing\Router;

class RouteFileRegistrar
{
    public function __construct(private Router $router)
    {
    }

    /**
     * Require the given routes file.
     *
     * @param string $routes The path to the routes file.
     */
    public function register(string $routes)
    {
        $router = $this->router;

        require $routes;
    }
}
