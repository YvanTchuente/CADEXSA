<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\ColumnMap as ColumnMapContract;

/**
 * Maps a database table column to a domain class field
 */
class ColumnMap implements ColumnMapContract
{
    protected string $columnName;

    protected string $fieldName;

    protected \ReflectionProperty $field;

    protected DataMap $dataMap;

    public function __construct(string $columnName, string $fieldName, DataMap $dataMap)
    {
        switch (true) {
            case (!$columnName):
                throw new \DomainException('Unknown column name');
                break;
            case (!$fieldName):
                throw new \DomainException('Unknown class field name');
                break;
        }
        $this->columnName = $columnName;
        $this->fieldName = $fieldName;
        $this->dataMap = $dataMap;
        $this->initField();
    }

    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getFieldValue(object $subject)
    {
        return $this->field->getValue($subject);
    }

    private function initField()
    {
        $class = $this->dataMap->getEntityClass();
        $class = new \ReflectionClass($class);
        try {
            $field = $class->getProperty($this->fieldName);
        } catch (\Exception $e) {
            throw new \RuntimeException("Unable to set up field '$this->fieldName'");
        }
        $field->setAccessible(true);
        $this->field = $field;
    }
}
