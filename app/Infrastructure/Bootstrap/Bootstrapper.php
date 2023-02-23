<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;

interface Bootstrapper
{
    public function bootstrap(Application $app);
}
