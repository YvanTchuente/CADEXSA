<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

trait NullEntityTrait
{
    public function __construct()
    {
    }

    public function getId()
    {
        return 0;
    }

    protected function setId(int $id)
    {
    }

    public function getVersion()
    {
        return 0;
    }

    public function setVersion(int $version)
    {
    }

    public function incrementVersion()
    {
    }
}
