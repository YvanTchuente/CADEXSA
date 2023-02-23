<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;
use Cadexsa\Infrastructure\Transaction\TransactionManager;


class RelationalStrategy implements RepositoryStrategy
{
    public function add(Entity $entity): void
    {
        TransactionManager::new($entity);
    }

    public function remove(Entity $entity): void
    {
        TransactionManager::deleted($entity);
    }

    public function removeAll(Criteria $criteria, Repository $repository): void
    {
        $entities = $this->selectMatching($criteria, $repository);
        foreach ($entities as $entity) {
            TransactionManager::deleted($entity);
        }
    }

    public function selectMatching(Criteria $criteria, Repository $repository): array
    {
        $entity = $repository->getEntityClass();
        $query = new Query($entity, $criteria);
        $results = $query->execute();

        return $results;
    }

    public function all(Repository $repository = null): array
    {
        $entity = $repository->getEntityClass();
        $query = new Query($entity);
        $results = $query->execute();

        return $results;
    }

    public function count(Repository $repository): int
    {
        $entity = $repository->getEntityClass();
        $table = MapperRegistry::getMapper($entity)->getDataMap()->getTableName();
        $stmt = app()->database->getConnection()->pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        
        return $count;
    }
}
