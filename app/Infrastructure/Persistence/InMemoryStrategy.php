<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;

class InMemoryStrategy implements RepositoryStrategy
{
    /**
     * @var Entity[]
     */
    private array $entities = [];

    public function selectMatching(Specification $criteria, Repository $repository): array
    {
        $results = [];
        foreach ($this->entities as $group) {
            foreach ($group as $entity)
                if ($criteria->isSatisfiedBy($entity)) {
                    $results[] = $entity;
                }
        }
        return $results;
    }

    public function add(Entity $entity): void
    {
        $id = $entity->getId();
        $class = get_class($entity);
        $this->entities[$class][$id] = $entity;
    }

    public function remove(Entity $entity): void
    {
        $id = $entity->getId();
        $class = get_class($entity);
        unset($this->entities[$class][$id]);
    }

    public function removeAll(Specification $criteria, Repository $repository): void
    {
        $entities = $this->selectMatching($criteria, $repository);
        foreach ($entities as $entity) {
            $this->remove($entity);
        }
    }

    public function all(?Repository $repository): array
    {
        return $this->entities;
    }

    public function count(?Repository $repository): int
    {
        return count($this->entities);
    }
}
