<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

abstract class CompositeCriteria
{
    /**
     * @var Criteria[]
     */
    protected array $criteria = [];

    public function __construct(Criteria ...$criteria)
    {
        $this->criteria = $criteria;
    }

    public function addCriteria(Criteria $criteria)
    {
        $this->criteria[] = $criteria;
        
        return $this;
    }
}
