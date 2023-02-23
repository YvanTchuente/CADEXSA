<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Domain\Model\Picture\Picture;

class PictureFactory extends EntityFactory
{
    /**
     * Creates a picture.
     */
    public static function create(string $location, string $description, string $shotOn): Picture
    {
        $id = app()->IdGenerator()->generateId();
        $picture = new Picture($id, $location, $description, $shotOn);

        return $picture;
    }

    /**
     * Reconstitutes a picture from its stored representation.
     * 
     * @param array $resultSet An associative array of record data.
     */
    public function reconstitute(array $resultSet): Picture
    {
        $this->validateResults($resultSet);
        extract($resultSet);

        // Reconstitute
        $picture = new Picture($id, $location, $description, $published_on);

        return $picture;
    }
}
