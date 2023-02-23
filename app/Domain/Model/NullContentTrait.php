<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

trait NullContentTrait
{
    use NullEntityTrait;

    public function getPublicationDate()
    {
        return new \DateTime;
    }

    public function setPublicationDate(string $timestamp)
    {
    }
}
