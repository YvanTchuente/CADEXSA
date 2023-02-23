<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;

class PictureFiltrationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $date = new \DateTime($params['month'] . " " . $params['year']);
        $page = (int) $params['page'] ?? 1;

        try {
            $criteria = Criteria::lessThan('shotOn', $date->format("Y-m-d"));
            $filteredPictures = Persistence::pictureRepository()->selectMatching($criteria);
            $paginator = new Paginator($filteredPictures, 8);
            $pictures = $paginator->getBatch($page);
            $view_params = ['pictures' => $pictures, 'pageCount' => $paginator->batchCount, 'page' => $page];
        } catch (\Throwable $e) {
            $view_params['msg'] = "There are no pictures for this period.";
        }

        $view_params['years'] = range((int) date('Y'), 2019);

        return $this->prepareResponseFromView(new View(views_path("gallery.php"), $view_params));
    }
}
