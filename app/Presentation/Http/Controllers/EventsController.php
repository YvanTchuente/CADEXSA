<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\Event\Status;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;

class EventsController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = (int) ($params['page'] ?? 1);
        $upcomingEvents = Persistence::eventRepository()->selectMatching(Criteria::equal('status', Status::UPCOMING));

        try {
            $paginator = new Paginator($upcomingEvents, 5);
            $events = $paginator->getBatch($page);
            $view_params = ['events' => $events, 'pageCount' => $paginator->batchCount, 'page' => $page];
        } catch (\Throwable $e) {
            $view_params['msg'] = "There are currently no upcoming events.";
        }

        $view_params['years'] = range((int) date('Y'), 2019);
        $events_view = new View(views_path("events.php"), $view_params);

        return $this->prepareResponseFromView($events_view);
    }
}
