<?php

declare(strict_types=1);

namespace Application\CMS;

use Psr\Http\Message\RequestInterface;

class EventChangeDetector
{
    protected const TABLE = 'events';

    /**
     * ID of the event article
     * 
     * @var int
     */
    protected $eventID;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CMSManager
     */
    protected $EventManager;

    public function __construct(RequestInterface $request, int $eventID, CMSManager $EventManager)
    {
        $this->request = $request;
        $this->eventID = $eventID;
        $this->EventManager = $EventManager;
    }

    public function detect()
    {
        // Data received from the request
        $params = json_decode($this->request->getBody()->getContents());
        $req_title = $params->title;
        $req_venue = $params->venue;
        $req_deadline = $params->deadline;
        $req_deadline_time = $params->deadline_time;
        $req_body = $params->body;

        // Data from the database to compare with for changes
        $event = $this->EventManager->get($this->eventID);
        $title = $event->getTitle();
        $venue = $event->getVenue();
        $deadline = date("Y-m-d", strtotime($event->getDeadlineDate()));
        $deadline_time = date("H:i", strtotime($event->getDeadlineDate()));
        $body = $event->getBody();

        if ($title !== $req_title) {
            $changes['title'] = $req_title;
        }
        if ($venue !== $req_venue) {
            $changes['venue'] = $req_venue;
        }
        if ($body !== $req_body) {
            $changes['description'] = $req_body;
        }
        if ($deadline !== $req_deadline) {
            $changes['deadline'] = $req_deadline . " " . $deadline_time;
            if ($deadline_time !== $req_deadline_time) {
                $changes['deadline'] = $req_deadline . " " . $req_deadline_time;
            }
        } elseif ($deadline_time !== $req_deadline_time) {
            $changes['deadline'] = $deadline . " " . $req_deadline_time;
            if ($deadline !== $req_deadline) {
                $changes['deadline'] = $req_deadline . " " . $req_deadline_time;
            }
        }

        if (isset($changes)) {
            return $changes;
        } else {
            return false;
        }
    }
}
