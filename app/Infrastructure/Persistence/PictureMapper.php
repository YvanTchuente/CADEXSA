<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Picture\Picture;
use Cadexsa\Domain\Factories\PictureFactory;

class PictureMapper extends Mapper
{
    protected function getEntityName(): string
    {
        return 'Picture';
    }

    protected function doLoad(array $resultSet)
    {
        $pictureFactory = new PictureFactory($this->dataMap);
        $picture = $pictureFactory->reconstitute($resultSet);
        return $picture;
    }

    /**
     * @param Picture $entity
     */
    protected function doValidateContent($entity)
    {
        switch (true) {
            case (!$entity->getLocation()):
                throw new \LengthException("Invalid picture path.");
                break;
            case (!$entity->getDescription()):
                throw new \LengthException('Invalid picture description.');
                break;
            case (!$entity->shotOn()):
                throw new \LengthException("Invalid picture creation date.");
                break;
            case (!$entity->getPublicationDate()):
                throw new \LengthException("Invalid picture publication date.");
                break;
        }
    }
}
