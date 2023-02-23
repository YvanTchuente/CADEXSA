<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class EventPublicationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        if ($method == "POST") {
            $name = $request->getParsedBody()['name'];
            $description = $request->getParsedBody()['description'];
            $occursOn = implode(" ", [$request->getParsedBody()['occursOn'], $request->getParsedBody()['occursAt']]);
            $venue = $request->getParsedBody()['venue'];
            $image = (string) $request->getUri()->withPath($request->getParsedBody()['image']);

            try {
                $event = Registry::eventService()->publishEvent($name, $description, $occursOn, $venue, $image);
                TransactionManager::commit();
                header('Location: /events/' . urlencode($event->getName()));
                exit();
            } catch (\Exception $e) {
                $view_params['message'] = "An error occurred during the creation process.";
            }
        }

        try {
            $pictures = (new Paginator(Persistence::pictureRepository()->all(), 8))->getBatch(1);
            $view_params['pictures'] = $pictures;
        } catch (\Throwable $e) {
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();
        $planner_view = new View(views_path("event_publisher.php"), $view_params ?? []);

        return $this->prepareResponseFromView($planner_view);
    }
}
