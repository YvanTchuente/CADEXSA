<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Domain\Model\Event\Status;

class EventFactory extends EntityFactory
{
    /**
     * Creates an event.
     */
    public static function create(string $name, string $description, string $occurrenceDate, string $venue, string $image): Event
    {
        $id = app()->IdGenerator()->generateId();
        $event = new Event($id, $name, $description, $occurrenceDate, $venue, $image);

        return $event;
    }

    /**
     * Reconstitutes an event from its stored representation.
     * 
     * @param array $resultSet An associative array of record data.
     */
    public function reconstitute(array $resultSet): Event
    {
        $this->validateResults($resultSet);
        extract($resultSet);
        $status = Status::from((int) $status);

        // Reconstitute
        $event = new Event($id, $name, $description, $occurs_on, $venue, $image, $published_on);

        return $event;
    }
}
