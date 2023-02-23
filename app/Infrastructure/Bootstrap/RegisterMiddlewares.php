<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;
use Cadexsa\Presentation\Authorization\Gate;
use Tym\Http\Message\Compression\Compressor;
use Cadexsa\Presentation\Http\Middleware\CompressResponse;
use Cadexsa\Presentation\Http\Middleware\AuthorizeRequests;

class RegisterMiddlewares implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        $app_middleware = config('app.middlewareGroups.web');

        foreach ($app_middleware as $middleware) {
            switch ($middleware) {
                case AuthorizeRequests::class:
                    $gate = new Gate($app, function () {
                        return user();
                    });

                    Application::setHttpMessageFactories($gate);
                    require $app->basePath('access-control.php');

                    $middleware = new $middleware($gate);
                    break;

                case CompressResponse::class:
                    $streamFactoryClass = config('app.factories.streamFactory');
                    $streamFactory = new $streamFactoryClass;

                    $compressor = new Compressor($streamFactory);
                    $middleware = new $middleware($compressor);
                    break;

                default:
                    $middleware = new $middleware;
                    break;
            }

            $middlewares[] = $middleware;
        }

        $app->setMiddlewares($middlewares);
    }
}
