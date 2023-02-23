<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;

class LoadRoutes implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        $router = $app->getRouter();

        $router->group(['middleware' => 'web'], $app->basePath('routes/web.php'));

        $router->group(['middleware' => 'api'], $app->basePath('routes/api.php'));
    }
}
