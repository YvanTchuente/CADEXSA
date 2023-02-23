<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Domain\Factories\EventFactory;

/**
 * Event data mapper
 */
class EventMapper extends Mapper
{
    protected function getEntityName(): string
    {
        return 'Event';
    }

    protected function doLoad(array $resultSet)
    {
        $eventFactory = new EventFactory($this->dataMap);
        $event = $eventFactory->reconstitute($resultSet);
        return $event;
    }

    /**
     * @param Event $entity
     */
    protected function doValidateContent($entity)
    {
        switch (true) {
            case (!$entity->getDescription()):
                throw new \DomainException('Invalid event description.');
                break;
            case (!$entity->getName()):
                throw new \DomainException('Invalid event name.');
                break;
            case (!$entity->getVenue()):
                throw new \DomainException('Invalid event venue.');
                break;
            case (!$entity->getStatus()):
                throw new \DomainException("Invalid event status.");
                break;
            case (!$entity->getImage()):
                throw new \DomainException('Invalid event image.');
                break;
            case (!$entity->getOccurrenceDate()):
                throw new \DomainException("Invalid timestamp.");
                break;
            case (!$entity->getPublicationDate()):
                throw new \DomainException("Invalid timestamp.");
                break;
        }
    }
}
