<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Dotenv\Dotenv;
use Cadexsa\Infrastructure\Application;
use Cadexsa\Infrastructure\Environment;

class LoadEnvironmentVariables implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        $this->createDotenv($app)->safeLoad();
    }

    /**
     * Create a Dotenv instance.
     */
    protected function createDotenv($app): Dotenv
    {
        return Dotenv::create(
            Environment::getRepository(),
            $app->environmentPath(),
            $app->environmentFile()
        );
    }
}
