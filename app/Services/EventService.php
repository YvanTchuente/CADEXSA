<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Factories\EventFactory;
use Cadexsa\Infrastructure\Messaging\NewsletterService;

class EventService
{
    /**
     * Publish an event.
     *
     * @param string $name The name of the event.
     * @param string $description A description of the event.
     * @param string $occursOn The timestamp at which the event will occur.
     * @param string $venue The venue of the event.
     * @param string $image The URI of a representative image of the event.
     * 
     * @return Event The event.
     */
    public function publishEvent(string $name, string $description, string $occursOn, string $venue, string $image): Event
    {
        $event = EventFactory::create($name, $description, $occursOn, $venue, $image);
        Persistence::eventRepository()->add($event);
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $link = $host . "/events/" . urlencode($name);
        $summary = $event->getDescription(true);
        $params = ['host' => $host, 'id' => $event->getId(), 'title' => $event->getName(), 'description' => $summary['description'], 'image' => $event->getImage(), 'occursOn' => new \DateTime($occursOn), 'link' => $link];
        $new_event_email_view = new View(views_path('emails/new_event_email'), $params);
        $newsletterService = new NewsletterService;
        try {
            $newsletterService->broadcastNewsletter($new_event_email_view->render(), 'Event: ' . $event->getName(), 'Cadexsa events alert');
        } catch (\Exception $e) {
            // Do nothing
        }
        return $event;
    }
}
