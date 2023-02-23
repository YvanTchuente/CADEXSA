<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

class ConjunctionCriteria extends CompositeCriteria implements Criteria
{
    public function toSql(DataMap $dataMap): string
    {
        foreach ($this->criteria as $criteria) {
            $clauses[] = $criteria->toSql($dataMap);
        }
        $result = implode(" AND ", $clauses);

        return $result;
    }

    public function isSatisfiedBy(Entity $entity)
    {
        return count(array_filter($this->criteria, function ($criteria) use ($entity) {
            return $criteria->isSatisfiedBy($entity);
        })) === count($this->criteria);
    }
}
