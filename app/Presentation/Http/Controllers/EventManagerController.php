<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;

class EventManagerController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $eventsPaginator = new Paginator(Persistence::eventRepository()->all(), 3);
            $events = $eventsPaginator->getBatch(1);
            $view_params['events'] = $events;
        } catch (\Throwable $e) {
            // Do nothing
        }
        $manager_view = new View(views_path("event_manager.php"), $view_params);
        return $this->prepareResponseFromView($manager_view);
    }
}
