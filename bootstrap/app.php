<?php

use Cadexsa\Infrastructure\Application;

// Create the application
$app = new Application(dirname(__DIR__));

$app->bootstrap();

return $app;
