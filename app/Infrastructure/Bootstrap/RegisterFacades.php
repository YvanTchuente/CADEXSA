<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;
use Cadexsa\Infrastructure\Facades\Facade;

class RegisterFacades implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        Facade::setFacadeApplication($app);
    }
}
