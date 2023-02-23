<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Domain\Model\News\Status as NewsStatus;
use Cadexsa\Domain\Model\Event\Status as EventStatus;

class HomeController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $publishedNews = Persistence::newsArticleRepository()->selectMatching(Criteria::equal('status', NewsStatus::PUBLISHED));
            $newsPaginator = new Paginator($publishedNews, 5);
            $news = $newsPaginator->getBatch(1);
            $view_params['news'] = $news;
        } catch (\Throwable $e) {
            // Do nothing
        }

        try {
            $publishedEvents = Persistence::eventRepository()->selectMatching(Criteria::equal('status', EventStatus::UPCOMING));
            $eventsPaginator = new Paginator($publishedEvents, 5);
            $events = $eventsPaginator->getBatch(1);
            $view_params['events'] = $events;
            $view_params['event_count'] = Persistence::eventRepository()->count();
        } catch (\Throwable $e) {
            $view_params['event_count'] = 0;
        }

        $view_params['member_count'] = Persistence::exStudentRepository()->count();
        $view_params['picture_count'] = Persistence::pictureRepository()->count();

        return $this->prepareResponseFromView(new View(views_path("home.php"), $view_params));
    }
}
