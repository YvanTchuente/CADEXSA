<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Infrastructure\Persistence\DataMap;

abstract class EntityFactory
{
    protected DataMap $dataMap;

    public function __construct(DataMap $dataMap)
    {
        $this->dataMap = $dataMap;
    }

    protected function validateResults(array $resultSet)
    {
        $columnList = $this->dataMap->columnList();

        foreach ($columnList as $column) {
            if (!array_key_exists($column, $resultSet)) {
                throw new \RuntimeException("The result set is not valid because the column '$column' is missing.");
            }
        }
    }
}
