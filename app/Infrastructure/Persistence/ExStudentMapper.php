<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\ExStudent\ExStudent;
use Cadexsa\Domain\Factories\ExStudentFactory;

class ExStudentMapper extends Mapper
{
    protected function getEntityName(): string
    {
        return 'ExStudent';
    }

    protected function doLoad(array $resultSet)
    {
        $exStudentFactory = new ExStudentFactory($this->dataMap);
        $exstudent = $exStudentFactory->reconstitute($resultSet);
        return $exstudent;
    }

    /**
     * @param ExStudent $entity
     */
    protected function doValidateContent($entity)
    {
        switch (true) {
            case (!$entity->getName()):
                throw new \DomainException('Invalid ex-student name.');
                break;
            case (!$entity->getUsername()):
                throw new \DomainException('Invalid ex-student username.');
                break;
            case (!$entity->getPhoneNumber()):
                throw new \DomainException('Invalid ex-student phone number.');
                break;
            case (!$entity->getAddress()):
                throw new \DomainException('Invalid ex-student address.');
                break;
            case (!$entity->getBatchYear()):
                throw new \DomainException('Invalid ex-student batch year.');
                break;
            case (!$entity->getOrientation()):
                throw new \DomainException('Invalid ex-student orientation.');
                break;
            case (!$entity->getLevel()):
                throw new \DomainException('Invalid ex-student level.');
                break;
            case (!$entity->getDescription()):
                throw new \DomainException('Invalid ex-student description.');
                break;
            case (!$entity->getRegistrationDate()):
                throw new \DomainException('Invalid ex-student registration timestamp.');
                break;
        }
    }
}
