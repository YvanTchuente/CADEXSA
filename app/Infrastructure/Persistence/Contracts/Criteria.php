<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence\Contracts;

use Cadexsa\Infrastructure\Persistence\DataMap;

/**
 * Defines a selection criteria.
 */
interface Criteria extends Specification
{
    /**
     * Converts the criteria into an SQL select **where** clause.
     *
     * @param DataMap $dataMap
     */
    public function toSql(DataMap $dataMap): string;
}
