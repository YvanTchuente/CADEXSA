<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\ColumnMap;

/**
 * Maps a database table to a domain entity class 
 */
class DataMap
{
    private string $domainClass;

    private string $tableName;

    /**
     * @var ColumnMap[]
     */
    private array $columnMaps = [];

    public function __construct(string $tableName, string $domainClass)
    {
        if (!class_exists($domainClass)) {
            throw new \RuntimeException("$domainClass class does not exists");
        }
        $this->domainClass = $domainClass;
        $this->tableName = $tableName;
    }

    public function getEntityClass()
    {
        return $this->domainClass;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getColumnMaps()
    {
        return $this->columnMaps;
    }

    public function addColumn(ColumnMap $columnMap)
    {
        $this->columnMaps[$columnMap->getFieldName()] = $columnMap;
    }

    /**
     * Gets the list of columns.
     * 
     * @return string[]
     */
    public function columnList()
    {
        $columns = [];

        foreach ($this->columnMaps as $columnMap) {
            $columnNames = $columnMap->getColumnName();
            if (is_array($columnNames)) {
                $columns = array_merge($columns, $columnNames);
            } else {
                $columnName = $columnNames;
                $columns[] = $columnName;
            }
        }

        return $columns;
    }

    public function getColumnsForField(string $fieldName)
    {
        foreach ($this->columnMaps as $columnMap) {
            if ($columnMap->getFieldName() == $fieldName) {
                return $columnMap->getColumnName();
            }
        }
        throw new \RuntimeException("Unable to find column for '$fieldName'");
    }

    public function insertList()
    {
        $placeholders = [];
        foreach ($this->columnMaps as $columnMap) {
            $columnNames = $columnMap->getColumnName();
            if (is_array($columnNames)) {
                foreach ($columnNames as $columnName) {
                    $placeholders[] = ":$columnName";
                }
            } else {
                $columnName = $columnNames;
                $placeholders[] = ":$columnName";
            }
        }
        $result = implode(', ', $placeholders);
        return $result;
    }

    public function updateList()
    {
        $placeholders = [];

        foreach ($this->columnMaps as $columnMap) {
            $columnNames = $columnMap->getColumnName();

            if (is_array($columnNames)) {
                foreach ($columnNames as $columnName) {
                    $placeholders[] = "$columnName = :$columnName";
                }
            } else {
                $columnName = $columnNames;
                $placeholders[] = "$columnName = :$columnName";
            }
        }

        $result = implode(', ', $placeholders);
        return $result;
    }
}
