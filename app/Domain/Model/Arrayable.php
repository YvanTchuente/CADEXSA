<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

interface Arrayable
{
    /**
     * Converts the instance to an array.
     *
     * @return array
     */
    public function toArray();
}
