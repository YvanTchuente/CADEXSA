<?php

use Cadexsa\Infrastructure\Facades\Router;
use Cadexsa\Presentation\Http\Controllers\MemberController;

Router::match(['GET'], '/api/exstudents/{username}', MemberController::class);
