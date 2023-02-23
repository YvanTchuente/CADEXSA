<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

/**
 * Domain entity supertype
 */
abstract class Entity implements Arrayable, \JsonSerializable
{
    /**
     * The identifier.
     */
    protected int $id;

    /**
     * The current version number of this instance.
     */
    protected int $version;

    public function __construct(int $id)
    {
        if (!$id) {
            throw new \DomainException("The identifier must be a non-zero number.");
        }
        $this->id = $id;
    }

    /**
     * Retrieves the identifier.
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion(int $version)
    {
        $this->version = $version;
    }

    public function incrementVersion()
    {
        return $this->version++;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray()
    {
        $properties = (new \ReflectionClass(static::class))->getProperties();
        foreach ($properties as $property) {
            if ($property->getName() === "version") {
                continue;
            }
            $results[$property->getName()] = $property->getValue($this);
        }

        return $results;
    }

    /**
     * Vaidates a given timestamp.
     *
     * @param string $timestamp The timestamp to check.

     * @throws \LogicException
     */
    protected function validateTimestamp(string $timestamp)
    {
        if (!strtotime($timestamp)) {
            throw new \LogicException("$timestamp is not a valid timestamp.");
        }

        return $timestamp;
    }
}
