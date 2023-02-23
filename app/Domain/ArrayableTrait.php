<?php

declare(strict_types=1);

namespace Cadexsa\Domain;

trait ArrayableTrait
{
    public function toArray()
    {
        $properties = (new \ReflectionClass(static::class))->getProperties();
        foreach ($properties as $property) {
            $results[$property->getName()] = $property->getValue($this);
        }

        return $results;
    }
}
