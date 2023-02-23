<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;

class NewsManagerController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $paginator = new Paginator(Persistence::newsArticleRepository()->all(), 3);
            $news = $paginator->getBatch(1);
            $view_params['news'] = $news;
        } catch (\Throwable $e) {
            // Do nothing
        }

        return $this->prepareResponseFromView(new View(views_path("news_manager.php"), $view_params));
    }
}
