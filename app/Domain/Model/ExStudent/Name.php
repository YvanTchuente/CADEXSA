<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\ArrayableTrait;
use Cadexsa\Domain\Model\Arrayable;

class Name implements Arrayable, \JsonSerializable
{
    use ArrayableTrait;

    private string $firstname;

    private string $lastname;

    public function __construct(string $firstname, string $lastname)
    {
        if (!$firstname) {
            throw new \LengthException("The first name is required.");
        }
        if (!$lastname) {
            throw new \LengthException("The last name is required.");
        }

        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function withFirstname(string $firstname)
    {
        if (!$firstname) {
            throw new \LengthException("The first name is empty.");
        }

        $instance = clone $this;
        $instance->firstname = $firstname;

        return $instance;
    }

    public function withLastname(string $lastname)
    {
        if (!$lastname) {
            throw new \LengthException("The last name is empty.");
        }

        $instance = clone $this;
        $instance->firstname = $lastname;
        
        return $instance;
    }

    public function __toString()
    {
        return ucwords(implode(" ", [$this->lastname, $this->firstname]));
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
