<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\News\Status;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Cadexsa\Domain\Model\News\NewsArticle;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Domain\Exceptions\ModelNotFoundException;
use Cadexsa\Infrastructure\Persistence\MapperRegistry;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class NewsEditController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());
        switch ($method) {
            case "GET":
                $params = $request->getQueryParams();
                if (!isset($params['id'])) {
                    throw new ModelNotFoundException;
                }
                $newsArticleId = (int) $params['id'];
                $newsArticle = Persistence::newsArticleRepository()->findById($newsArticleId);
                if ($newsArticle instanceof INull) {
                    throw new ModelNotFoundException;
                }
                break;

            case "POST":
                $newsArticleId = (int) $request->getParsedBody()['newsArticleId'];
                $action = $request->getParsedBody()['action'];
                $title = $request->getParsedBody()['title'];
                $tagList = explode("; ", $request->getParsedBody()['tags']);
                $body = $request->getParsedBody()['body'];
                $authorId = (int) $request->getParsedBody()['authorId'];
                $newsArticle = Persistence::newsArticleRepository()->findById($newsArticleId);

                try {
                    try {
                        foreach ($tagList as $tag) {
                            $tags[] = Tag::from(strtolower($tag));
                        }
                    } catch (\Throwable $e) {
                        preg_match("/^\"(\w+)\"/", $e->getMessage(), $matches);
                        $tag = $matches[1];
                        throw new \RuntimeException("$tag is not a valid tag");
                    }
                    $newsArticle->setTitle($title);
                    $newsArticle->setTags(...$tags);
                    $newsArticle->setBody($body);
                    $newsArticle->setAuthor($authorId);
                    if (!MapperRegistry::getMapper(NewsArticle::class)->hasChanged($newsArticle)) {
                        throw new \RuntimeException("No changes were detected");
                    }
                    switch ($action) {
                        case 'publish':
                            if ($newsArticle->published()) {
                                throw new \RuntimeException("Article already published");
                            }
                            $newsArticle->setStatus(Status::PUBLISHED);
                            break;
                        case 'save':
                            $view_params['message'] = "Article saved for future edits and/or publication";
                            break;
                    }
                    TransactionManager::dirty($newsArticle);
                    TransactionManager::commit();
                    if ($action == 'publish') {
                        header('Location: /news/' . urlencode($newsArticle->getTitle()));
                        exit();
                    }
                } catch (\Throwable $e) {
                    $params['error'] = $e->getMessage();
                }
                break;
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();

        foreach (Tag::cases() as $tag) {
            $view_params['tags'][] = $tag->label();
        }

        $view_params['newsArticle'] = $newsArticle;
        $view_params['authorId'] = user()->getId();
        $editor_view = new View(views_path("news_editor.php"), $view_params);

        return $this->prepareResponseFromView($editor_view);
    }
}
