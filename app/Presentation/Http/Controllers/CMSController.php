<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;

class CMSController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $newsPaginator = new Paginator(Persistence::newsArticleRepository()->all(), 3);
            $news = $newsPaginator->getBatch(1);
            $view_params['news'] = $news;
        } catch (\Throwable $e) {
            // Do nothing
        }

        try {
            $eventsPaginator = new Paginator(Persistence::eventRepository()->all(), 3);
            $events = $eventsPaginator->getBatch(1);
            $view_params['events'] = $events;
        } catch (\Throwable $e) {
            // Do nothing
        }

        return $this->prepareResponseFromView(new View(views_path("cms.php"), $view_params));
    }
}
