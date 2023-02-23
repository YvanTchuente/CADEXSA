<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;

class Criteria implements CriteriaContract
{
    private string $field;

    private string $operator;

    private mixed $value;

    public function __construct(string $field, string $operator, int|float|string|\BackedEnum $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        switch (true) {
            case (is_string($value)):
                $value = "'$value'";
                break;
            case ($value instanceof \BackedEnum):
                $value = "'" . $value->value . "'";
                break;
        }
        $this->value = $value;
    }

    public static function matches(string $field, int|float|string|\BackedEnum $value)
    {
        if (!preg_match('/%/', $value)) {
            $value = "%$value%";
        }
        return new Criteria($field, 'LIKE', $value);
    }

    public static function equal(string $field, int|float|string|\BackedEnum $value)
    {
        return new Criteria($field, '=', $value);
    }

    public static function IsNot(string $field, int|float|string|\BackedEnum $value)
    {
        return new Criteria($field, '<>', $value);
    }

    public static function lessThan(string $field, int|float|string|\BackedEnum $value)
    {
        return new Criteria($field, '<', $value);
    }

    public static function greaterThan(string $field, int|float|string|\BackedEnum $value)
    {
        return new Criteria($field, '>', $value);
    }

    public function toSql(DataMap $dataMap): string
    {
        $columns = $dataMap->getColumnsForField($this->field);

        if (is_array($columns)) {
            $filters = [];
            foreach ($columns as $column) {
                $filters[] = $column . ' ' . $this->operator . ' ' . $this->value;
            }
            $sql = implode(' OR ', $filters);
        } else {
            $column = $columns;
            $sql = $column . ' ' . $this->operator . ' ' . $this->value;
        }

        return $sql;
    }

    public function isSatisfiedBy(Entity $entity)
    {
        $entity_class = get_class($entity);
        $field = new \ReflectionProperty($entity_class, $this->field);
        $value = $field->getValue($entity);

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        return $value === $this->value;
    }

    public function and(CriteriaContract $criteria): CriteriaContract
    {
        return new ConjunctionCriteria($this, $criteria);
    }

    public function or(CriteriaContract $criteria): CriteriaContract
    {
        return new DisjunctionCriteria($this, $criteria);
    }
}
