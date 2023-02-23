<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\ArrayableTrait;
use Cadexsa\Domain\Model\Arrayable;

class Address implements Arrayable, \JsonSerializable
{
    use ArrayableTrait;

    private string $country;

    private string $city;

    public function __construct(string $country, string $city)
    {
        self::validateCountry($country);
        if (!$city) {
            throw new \DomainException('Unknown city.');
        }
        $this->country = $country;
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function withCity(string $city)
    {
        if (!$city) {
            throw new \InvalidArgumentException('Invalid city.');
        }
        
        $instance = clone $this;
        $instance->city = $city;

        return $instance;
    }

    public function withCountry(string $country)
    {
        self::validateCountry($country);

        $instance = clone $this;
        $instance->country = $country;

        return $instance;
    }

    public function __toString()
    {
        return ucwords(implode(', ', [$this->city, $this->country]));
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public static function validateCountry(string $country)
    {
        $data = file_get_contents(public_path('data/valid_countries.json'));
        $valid_countries = (json_decode($data))->countries;

        if (!in_array(ucwords($country), $valid_countries)) {
            throw new \DomainException('Unknown country.');
        }
    }
}
