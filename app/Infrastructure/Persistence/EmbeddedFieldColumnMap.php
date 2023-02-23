<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\ColumnMap;

/**
 * Maps one or multiple database table columns to an embedded field of a domain class
 */
class EmbeddedFieldColumnMap implements ColumnMap
{
    private string $fieldName;

    private array $columnNames;

    private \ReflectionProperty $field;

    private DataMap $dataMap;

    public function __construct(DataMap $dataMap, string $fieldName, array ...$attributes)
    {
        if (!$fieldName) {
            throw new \DomainException('Unknown class field name');
        }
        $this->dataMap = $dataMap;
        $this->fieldName = $fieldName;
        foreach ($attributes as $map) {
            $attributeName = $map['name'];
            $this->columnNames[$attributeName] = $map['column'];
        }
        $this->initField();
    }

    public function getColumnName()
    {
        return array_values($this->columnNames);
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getFieldValue(object $subject)
    {
        $values = [];
        $fieldValue = $this->field->getValue($subject);
        $reflectedObject = new \ReflectionObject($fieldValue);
        $properties = $reflectedObject->getProperties();
        foreach ($properties as $property) {
            $columnName = $this->columnNames[$property->getName()];
            if ($columnName) {
                $value = $property->getValue($fieldValue);
                $values[$columnName] = $value;
            }
        }
        return $values;
    }

    private function initField()
    {
        $class = $this->dataMap->getEntityClass();
        $class = new \ReflectionClass($class);
        try {
            $field = $class->getProperty($this->fieldName);
            $field->setAccessible(true);
            $this->field = $field;
        } catch (\Exception $e) {
            throw new \RuntimeException("Unable to set up field '$this->fieldName'");
        }
    }
}
