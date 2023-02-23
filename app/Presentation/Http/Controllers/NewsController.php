<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\News\Status;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;

class NewsController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = (int) ($params['page'] ?? 1);

        try {
            $publishedNews = Persistence::newsArticleRepository()->selectMatching(Criteria::equal('status', Status::PUBLISHED));
            $paginator = new Paginator($publishedNews, 8);
            $news = $paginator->getBatch($page);
            foreach (Tag::cases() as $tag) {
                $tags[] = $tag->label();
            }
            $view_params = ['news' => $news, 'tags' => $tags, 'pageCount' => $paginator->batchCount, 'page' => $page];
        } catch (\Throwable $e) {
            $view_params['msg'] = "There are currently no news articles.";
        }

        $view_params['years'] = range((int) date('Y'), 2019);

        return $this->prepareResponseFromView(new View(views_path("news.php"), $view_params));
    }
}
