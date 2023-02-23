<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Transaction;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\MapperRegistry;

/**
 * Represents a business transaction.
 * 
 * Tracks changes made during a transaction and at commit time,
 * it does any concurrency checking before writing out changes
 * to the database.
 */
class Transaction
{
    private IdentityMap $map;

    /**
     * @var Entity[]
     */
    private array $deletedEntities = [];

    public function __construct()
    {
        $this->map = new IdentityMap;
    }

    /**
     * Retrieves the identifier map.
     */
    public function getIdentityMap()
    {
        return $this->map;
    }

    /**
     * Registers an entity as clean.
     */
    public function clean(Entity &$entity)
    {
        $this->map->add($entity);
    }

    /**
     * Registers an entity as new.
     */
    public function new(Entity &$entity)
    {
        $this->map->add($entity, 'new');
    }

    /**
     * Registers an entity as modified.
     */
    public function dirty(Entity &$entity)
    {
        $this->map->add($entity, 'dirty');
    }

    /**
     * Registers an entity as deleted.
     */
    public function deleted(Entity &$entity)
    {
        $this->map->remove($entity);
        $this->deletedEntities[$entity->getId()] = $entity;
    }

    /**
     * Commits the changes.
     * 
     * Opens a system transaction to write out the changes to the database
     * but rolls back the transaction if any concurrency problem arises.
     * 
     * @throws \Throwable 
     */
    public function commit()
    {
        app()->database->getConnection()->pdo->beginTransaction();
        
        try {
            $this->checkConsistentReads();
            $this->insertNew();
            $this->removeDeleted();
            $this->updateDirty();
            
            app()->database->getConnection()->pdo->commit();
        } catch (\Throwable $e) {
            app()->database->getConnection()->pdo->rollBack();
            throw $e;
        }
    }

    private function checkConsistentReads()
    {
        $reads = $this->map->getIterator('clean');
        
        foreach ($reads as $dependent) {
            $dependent->incrementVersion();
        }
    }

    private function insertNew()
    {
        $new_entities = $this->map->getIterator('new');

        foreach ($new_entities as $entity) {
            $class = get_class($entity);
            $mapper = MapperRegistry::getMapper($class);
            $mapper->insert($entity);
        }
    }

    private function updateDirty()
    {
        $updated_entities = $this->map->getIterator('dirty');

        foreach ($updated_entities as $entity) {
            $class = get_class($entity);
            $mapper = MapperRegistry::getMapper($class);
            $mapper->update($entity);
        }
    }

    private function removeDeleted()
    {
        foreach ($this->deletedEntities as $entity) {
            $class = get_class($entity);
            $mapper = MapperRegistry::getMapper($class);
            $mapper->delete($entity);
        }
    }
}
