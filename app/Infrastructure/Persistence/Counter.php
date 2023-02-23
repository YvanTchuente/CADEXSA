<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\IdGenerator;

class Counter implements IdGenerator
{
    private int $counter = 1;

    public function generateId()
    {
        $this->counter = $this->counter + 1;
        
        return $this->counter;
    }
}
