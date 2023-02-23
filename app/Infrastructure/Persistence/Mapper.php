<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Infrastructure\Transaction\TransactionManager;
use Cadexsa\Infrastructure\Transaction\ConcurrencyException;

/**
 * Data mapper layer supertype.
 */
abstract class Mapper
{
    /**
     * The version number of loaded entities.
     *
     * @var int[]
     */
    private static array $versions = [];

    /**
     * Map from a database table to a domain entity class.
     */
    protected DataMap $dataMap;

    public function __construct()
    {
        $this->loadMetadata();
    }

    /**
     * Finds an entity according to a where clauses
     * or retrieve all entities.
     *
     * @param string|null $where
     * @return Entity[]
     */
    public function find(string $where = null)
    {
        $table = $this->dataMap->getTableName();
        $stmt = "SELECT * FROM $table";

        if ($where) {
            $stmt .= " WHERE " . $where;
        }

        $entities = [];
        $connection = app()->database->getConnection();
        $stmt = $connection->pdo->prepare($stmt);
        $stmt->execute();

        $rs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rs as $row) {
            $entity = $this->load($row);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Saves an entity.
     */
    public function insert(Entity $entity)
    {
        $this->validateContent($entity);

        $connection = app()->database->getConnection();
        $columnList = implode(', ', $this->dataMap->columnList());
        $stmt = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->dataMap->getTableName(), $columnList, $this->dataMap->insertList());
        $stmt = $connection->pdo->prepare($stmt);
        $this->bindParams($entity, $stmt);
        $stmt->execute();
    }

    /**
     * Updates an entity.
     */
    public function update(Entity $entity)
    {
        $this->validateContent($entity);

        $version = self::$versions[$entity->getId()];
        $new_version = $version + 1;

        $connection = app()->database->getConnection();
        $stmt = "UPDATE " . $this->dataMap->getTableName() . " SET " . $this->dataMap->updateList() . ", modified_at = CURRENT_TIMESTAMP(), modified_by = :modifier, version = :new_version WHERE id = :id AND version = :version";
        $stmt = $connection->pdo->prepare($stmt);
        $stmt->bindValue("modifier", 'admin');
        $stmt->bindValue("new_version", $new_version, \PDO::PARAM_INT);
        $stmt->bindValue('id', $entity->getId(), \PDO::PARAM_INT);
        $stmt->bindValue("version", $version, \PDO::PARAM_INT);
        $this->bindParams($entity, $stmt);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $this->throwConcurrencyException($entity);
        }

        $entity->setVersion($new_version);
        self::$versions[$entity->getId()] = $new_version;

        TransactionManager::getCurrentTransaction()->clean($entity); // Mark the object as clean
    }

    /**
     * Deletes an entity.
     */
    public function delete(Entity $entity)
    {
        $this->validateEntity($entity);

        $version = self::$versions[$entity->getId()];
        TransactionManager::getCurrentTransaction()->getIdentityMap()->remove($entity);
        $stmt = "DELETE FROM " . $this->dataMap->getTableName() . " WHERE id = :id AND version = :version";

        $connection = app()->database->getConnection();
        $stmt = $connection->pdo->prepare($stmt);
        $stmt->bindValue("id", $entity->getId(), \PDO::PARAM_INT);
        $stmt->bindValue("version", $version, \PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $this->throwConcurrencyException($entity);
        }
    }

    public function getDataMap(): DataMap
    {
        return $this->dataMap;
    }

    public function hasChanged(Entity $entity): bool
    {
        $changeCount = 0;
        $rs = app()->database->getConnection()->pdo->query("SELECT * FROM " . $this->dataMap->getTableName() . " WHERE id = " . $entity->getId());
        $record = $rs->fetch(\PDO::FETCH_ASSOC);
        $columnMaps = $this->dataMap->getColumnMaps();

        foreach ($columnMaps as $columnMap) {
            $columnName = $columnMap->getColumnName();
            if (is_array($columnName)) {
                foreach ($columnName as $column) {
                    $fieldValue = $columnMap->getFieldValue($entity);
                    if ((string) $fieldValue[$column] !== (string) $record[$column]) {
                        $changeCount += 1;
                    }
                }
            } else {
                if ((string) $columnMap->getFieldValue($entity) !== (string) $record[$columnName]) {
                    $changeCount += 1;
                }
            }
        }

        return boolval($changeCount);
    }

    private function throwConcurrencyException(Entity $entity)
    {
        $checkVersionSQL = "SELECT version, modifiedAt, modifiedBy FROM " . $this->dataMap->getTableName() . " WHERE id = ?";

        $stmt = app()->database->getConnection()->pdo->prepare($checkVersionSQL);
        $stmt->bindValue(1, $entity->getId());
        $stmt->execute();

        $rs = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($rs) {
            extract($rs);
            if ($version > $entity->getVersion()) {
                $message = sprintf("%s %d modified by %s at %s.", $this->getEntityName(), $entity->getId(), $modifiedBy, $modifiedAt);
                throw new ConcurrencyException($message);
            } else {
                throw new \RuntimeException("Unexpected error while checking timestamp.");
            }
        } else {
            $message = sprintf("%s %d has been deleted.", $this->getEntityName(), $entity->getId());
            throw new ConcurrencyException($message);
        }
    }

    protected function loadMetadata()
    {
        $entity = $this->getEntityName();
        $parser = new MetadataParser(app()->basePath("/metadata/$entity.xml"));
        $dataMap = $parser->parse();
        $this->dataMap = $dataMap;
    }

    abstract protected function getEntityName(): string;

    protected function load(array $resultSet)
    {
        $id = $resultSet['id'];
        $map = TransactionManager::getCurrentTransaction()->getIdentityMap();
        if ($map->contains($id)) {
            return $map->get($id);
        }
        $entity = $this->doLoad($resultSet);
        $entity->setVersion($resultSet['version']);
        self::$versions[$entity->getId()] = $entity->getVersion();
        TransactionManager::getCurrentTransaction()->clean($entity);
        return $entity;
    }

    /**
     * @return Entity
     */
    abstract protected function doLoad(array $resultSet);

    final protected function validateEntity($entity)
    {
        $domainClass = $this->dataMap->getEntityClass();

        if (!is_a($entity, $domainClass)) {
            throw new \InvalidArgumentException("The object is not an instance of $domainClass");
        }
    }

    final protected function validateContent($entity)
    {
        $this->validateEntity($entity);
        $this->doValidateContent($entity);
    }

    abstract protected function doValidateContent($entity);

    protected function bindParams(Entity $subject, \PDOStatement &$stmt)
    {
        $columnMaps = $this->dataMap->getColumnMaps();

        foreach ($columnMaps as $columnMap) {
            $columnName = $columnMap->getColumnName();
            $fieldValue = $columnMap->getFieldValue($subject);

            if (is_array($columnName)) {
                foreach ($columnName as $column) {
                    $value = $fieldValue[$column];
                    $stmt->bindValue($column, $value);
                }
            } else {
                $stmt->bindValue($columnName, $fieldValue);
            }
        }
    }
}
