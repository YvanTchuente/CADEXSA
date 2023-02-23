<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->prepareResponseFromView(new View(views_path("not_found.php")), 404);
    }
}
