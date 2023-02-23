<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria;

interface RepositoryStrategy
{
    public function add(Entity $entity): void;

    public function remove(Entity $entity): void;

    public function removeAll(Criteria $criteria, Repository $repository): void;

    /**
     * @return Entity[]
     */
    public function selectMatching(Criteria $criteria, Repository $repository): array;

    /**
     * @return Entity[]
     */
    public function all(Repository $repository): array;

    public function count(Repository $repository): int;
}
