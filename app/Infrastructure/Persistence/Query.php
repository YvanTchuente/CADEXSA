<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

/**
 * Represents a database selection query.
 */
class Query
{
    /**
     * The class name of the entity to query.
     */
    private string $entity;

    /**
     * The selection criteria.
     */
    private Criteria $criteria;

    private DataMap $dataMap;

    /**
     * @param string $entity The class name of the entity to query.
     * @param Criteria|null $criteria The query's selection criteria.
     */
    public function __construct(string $entity, Criteria $criteria = null)
    {
        if (!class_exists($entity)) {
            throw new \InvalidArgumentException("'$entity' is not a defined domain entity class");
        }
        $this->entity = $entity;
        $this->dataMap = MapperRegistry::getMapper($this->entity)->getDataMap();
        if ($criteria) {
            $this->criteria = $criteria;
        }
    }

    /**
     * Sets the selection criteria.
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Executes the query.
     * 
     * @return Entity[] The selected entities.
     */
    public function execute()
    {
        if (isset($this->criteria)) {
            $where = $this->criteria->toSql($this->dataMap);
        } else {
            $where = null;
        }

        try {
            $mapper = MapperRegistry::getMapper($this->entity);
            $results = $mapper->find($where);
        } catch (\PDOException $e) {
            $results = [];
        }

        return $results;
    }
}
