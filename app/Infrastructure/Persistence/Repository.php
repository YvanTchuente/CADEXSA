<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

abstract class Repository
{
    public function __construct(protected RepositoryStrategy $strategy)
    {
    }

    /**
     * Deletes all items matching a given criteria.
     *
     * @param Criteria $criteria A selection criteria.
     */
    public function removeAll(Criteria $criteria)
    {
        $this->strategy->removeAll($criteria, $this);
    }

    /**
     * Counts all items in the repository.
     *
     * @return int The number of items in the repository .
     */
    public function count(): int
    {
        return $this->strategy->count($this);
    }

    abstract public function getEntityClass(): string;
}
