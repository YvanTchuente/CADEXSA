<?php

namespace Application\Generic;

interface Memento
{
    public function getState(): array;
    public function setState(array $state);
}
