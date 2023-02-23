<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\ColumnMap as ColumnMapContract;

class RelationalColumnMap extends ColumnMap implements ColumnMapContract
{
    private string $referencedField;

    public function __construct(string $columnName, string $fieldName, string $referencedField, DataMap $dataMap)
    {
        switch (true) {
            case (!$columnName):
                throw new \DomainException('Unknown column name');
                break;
            case (!$fieldName):
                throw new \DomainException('Unknown class field name');
                break;
            case (!$referencedField):
                throw new \DomainException('Unknown referenced field name');
                break;
        }
        $this->columnName = $columnName;
        $this->fieldName = $fieldName;
        $this->dataMap = $dataMap;
        $this->initField($referencedField);
    }

    public function getFieldValue(object $subject)
    {
        $fieldValue = $this->field->getValue($subject);
        $targetField = new \ReflectionProperty($fieldValue, $this->referencedField);
        return $targetField->getValue($fieldValue);
    }

    private function initField(string $referencedField)
    {
        $domainClass = $this->dataMap->getEntityClass();
        $domainClass = new \ReflectionClass($domainClass);
        try {
            $this->field = $domainClass->getProperty($this->fieldName);
        } catch (\Exception $e) {
            throw new \RuntimeException("Unable to set up field '$this->fieldName'");
        }
        $fieldClass = new \ReflectionClass($this->field->getType()->getName());
        if (!$fieldClass->hasProperty($referencedField)) {
            throw new \RuntimeException("The reference field '$referencedField' does not exist");
        }
        $this->referencedField = $referencedField;
    }
}
