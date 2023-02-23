<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Transaction;

use Cadexsa\Domain\Model\Entity;

/**
 * Maps the entities loaded during a business transaction to their identities.
 */
class IdentityMap
{
    /**
     * Clean entity objects
     * 
     * @var Entity[]
     */
    private array $cleanEntities = [];

    /**
     * New entity objects
     * 
     * @var Entity[]
     */
    private array $newEntities = [];

    /**
     * Modified entity objects
     * 
     * @var Entity[]
     */
    private array $modifiedEntities = [];

    /**
     * Retrieves an entity from the map.
     * 
     * @return Entity The entity.
     */
    public function get(int $entityId)
    {
        if (!$this->contains($entityId)) {
            throw new \RuntimeException("No entity with identifier $entityId was found in the map");
        }
        $list = match (true) {
            array_key_exists($entityId, $this->cleanEntities) => 'cleanEntities',
            array_key_exists($entityId, $this->newEntities) => 'newEntities',
            array_key_exists($entityId, $this->modifiedEntities) => 'modifiedEntities',
        };
        return $this->$list[$entityId];
    }

    /**
     * Determines whether the map contains an entity.

     * @return bool
     */
    public function contains(int $entityId)
    {
        $map = array_merge($this->cleanEntities, $this->newEntities, $this->modifiedEntities);
        foreach ($map as $obj) {
            if ($obj->getId() == $entityId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds an entity object to the map.
     *
     * @param string $category Category of objects in which to add the object. 
     *                         Possible values include: 'clean', 'new', 'dirty'.
     */
    public function add(Entity &$entity, string $category = 'clean')
    {
        $entityId = $entity->getId();
        switch ($category) {
            case 'clean':
                if (isset($this->cleanEntities[$entityId])) {
                    throw new \RuntimeException('Object is already registered as clean');
                }
                if (isset($this->newEntities[$entityId])) {
                    unset($this->newEntities[$entityId]);
                }
                if (isset($this->modifiedEntities[$entityId])) {
                    unset($this->modifiedEntities[$entityId]);
                }
                $this->cleanEntities[$entityId] = $entity;
                break;
            case 'new':
                if (isset($this->cleanEntities[$entityId])) {
                    throw new \RuntimeException('Object is already registered as clean');
                }
                if (isset($this->newEntities[$entityId])) {
                    throw new \RuntimeException('Object is already registered as new');
                }
                if (isset($this->modifiedEntities[$entityId])) {
                    throw new \RuntimeException('Object is already registered as modified');
                }
                $this->newEntities[$entityId] = $entity;
                break;
            case 'dirty':
                if (!isset($this->modifiedEntities[$entityId]) && !isset($this->newEntities[$entityId])) {
                    $this->modifiedEntities[$entityId] = $entity;
                }
                if (isset($this->cleanEntities[$entityId])) {
                    unset($this->cleanEntities[$entityId]);
                }
                break;
            default:
                throw new \DomainException('Unknown entity state');
                break;
        }
    }

    /**
     * Removes an entity from the map
     */
    public function remove(Entity &$entity)
    {
        $entityId = $entity->getId();
        switch (true) {
            case (isset($this->cleanEntities[$entityId])):
                unset($this->cleanEntities[$entityId]);
                break;
            case (isset($this->newEntities[$entityId])):
                unset($this->newEntities[$entityId]);
                break;
            case (isset($this->modifiedEntities[$entityId])):
                unset($this->modifiedEntities[$entityId]);
                break;
        }
    }

    /**
     * Retrieves an iterator for the entity objects of the map.
     *
     * @param string $category The category of objects for which to obtain an iterator.
     *                         The possible values include: 'new', 'clean', 'dirty'.
     */
    public function getIterator(string $category = null): iterable
    {
        switch ($category) {
            case 'new':
                $iterator = new \ArrayIterator($this->newEntities);
                break;
            case 'clean':
                $iterator = new \ArrayIterator($this->cleanEntities);
                break;
            case 'dirty':
                $iterator = new \ArrayIterator($this->modifiedEntities);
                break;
            default:
                $iterator = new \ArrayIterator(array_merge($this->cleanEntities, $this->newEntities, $this->modifiedEntities));
                break;
        }
        return $iterator;
    }
}
