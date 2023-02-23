<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Domain\Model\Event\MissingEvent;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;



class EventRepository extends Repository
{
    /**
     * Finds a event by its identifier.
     *
     * @param integer $eventId The identifier of a event.
     * @return Event The event.
     */
    public function findById(int $eventId): Event
    {
        $criteria = Criteria::equal('id', $eventId);
        $event = $this->selectMatch($criteria) ?? new MissingEvent;
        return $event;
    }

    /**
     * Selects the first event matching a given criteria.
     *
     * @param Criteria $criteria A selection criteria.
     * @return Event The event.
     */
    public function selectMatch(CriteriaContract $criteria): Event
    {
        return $this->strategy->selectMatching($criteria, $this)[0] ?? new MissingEvent;
    }

    /**
     * Selects events matching a given criteria.
     * 
     * @param Criteria $criteria A selection criteria.
     * @return Event[] A collection of events.
     */
    public function selectMatching(CriteriaContract $criteria): array
    {
        return $this->strategy->selectMatching($criteria, $this);
    }

    /**
     * Adds an event to the repository.
     *
     * @param Event $event The event.
     */
    public function add(Event $event)
    {
        $this->strategy->add($event);
    }

    /**
     * Removes an event from the repository.
     *
     * @param Event $event The event.
     */
    public function remove(Event $event)
    {
        $this->strategy->remove($event);
    }

    /**
     * Retrieves all events.
     * 
     * @return Event[] All events.
     */
    public function all(): array
    {
        return $this->strategy->all($this);
    }

    public function getEntityClass(): string
    {
        return MapperRegistry::getMapper(Event::class)->getDataMap()->getEntityClass();
    }
}
