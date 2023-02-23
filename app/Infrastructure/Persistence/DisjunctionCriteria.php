<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

class DisjunctionCriteria extends CompositeCriteria implements Criteria
{
    public function toSql(DataMap $dataMap): string
    {
        foreach ($this->criteria as $criteria) {
            $clause = $criteria->toSql($dataMap);
            $clauses[] = $clause;
        }
        $result = implode(" OR ", $clauses);

        return $result;
    }

    public function isSatisfiedBy(Entity $entity)
    {
        return count(array_filter($this->criteria, function ($criteria) use ($entity) {
            return $criteria->isSatisfiedBy($entity);
        }));
    }
}
