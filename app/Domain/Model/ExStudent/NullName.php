<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\INull;

class NullName extends Name implements INull
{
    public function __construct()
    {
    }

    public function getFirstname()
    {
        return "";
    }

    public function getLastname()
    {
        return "";
    }

    public function withFirstname(string $firstname)
    {
        return $this;
    }

    public function withLastname(string $lastname)
    {
        return $this;
    }

    public function __toString()
    {
        return "";
    }
}
