<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\INull;

class NullAddress extends Address implements INull
{
    public function __construct()
    {
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function withCity(string $city)
    {
        return $this;
    }

    public function withCountry(string $country)
    {
        return $this;
    }

    public function __toString()
    {
        return "";
    }
}
