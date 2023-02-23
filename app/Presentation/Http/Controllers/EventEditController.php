<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Domain\Exceptions\ModelNotFoundException;
use Cadexsa\Infrastructure\Persistence\MapperRegistry;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class EventEditController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        switch ($method) {
            case "get":
                $params = $request->getQueryParams();
                if (!isset($params['id'])) {
                    throw new ModelNotFoundException();
                }
                $eventId = (int) $params['id'];
                $event = Persistence::eventRepository()->findById($eventId);
                if ($event instanceof INull) {
                    throw new ModelNotFoundException;
                }
                break;

            case "post":
                $eventId = (int) $request->getParsedBody()['eventId'];
                $name = $request->getParsedBody()['name'];
                $description = $request->getParsedBody()['description'];
                $occursAt = $request->getParsedBody()['occursAt'];
                $occursOn = $request->getParsedBody()['occursOn'];
                $occursOn = implode(" ", [$occursOn, $occursAt]);
                $venue = $request->getParsedBody()['venue'];
                $event = Persistence::eventRepository()->findById($eventId);

                if ($event instanceof INull) {
                    throw new ModelNotFoundException;
                }

                try {
                    // Updates only if the event has really changed from its database representation
                    $event->setName($name);
                    $event->setDescription($description);
                    $event->setOccurrenceDate($occursOn);
                    $event->setVenue($venue);
                    if (!MapperRegistry::getMapper(Event::class)->hasChanged($event)) {
                        throw new \RuntimeException("No changes were detected");
                    }
                    TransactionManager::dirty($event);
                    TransactionManager::commit();
                    header('Location: /events/' . urlencode($event->getName()));
                    exit();
                } catch (\Exception $e) {
                    $params['error'] = $e->getMessage();
                    $_SESSION['token'] = $view_params['token'] = csrf_token();
                }
                break;
        }
        $view_params['event'] = $event;

        return $this->prepareResponseFromView(new View(views_path("event_editor.php"), $view_params));
    }
}
