<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;

class EventFiltrationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $date = new \DateTime($params['month'] . " " . $params['year']);
        $page = (int) $params['page'] ?? 1;
        try {
            $criteria = Criteria::lessThan('publishedOn', $date->format("Y-m-d"));
            $filteredEvents = Persistence::eventRepository()->selectMatching($criteria);
            $paginator = new Paginator($filteredEvents, 8);
            $events = $paginator->getBatch($page);
            $summaries = [];
            foreach ($events as $event) {
                $summary = $event->getDescription(true);
                $summary['link'] = "/events/" . urlencode(strtolower($event->getName()));
                $summaries[] = $summary;
            }
            $view_params = ['summaries' => $summaries, 'pageCount' => $paginator->batchCount, 'page' => $page];
        } catch (\Throwable $e) {
            $view_params['msg'] = "There are no matching events.";
        }
        $view_params['years'] = range((int) date('Y'), 2019);

        return $this->prepareResponseFromView(new View(views_path("events.php"), $view_params));
    }
}
