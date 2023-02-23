<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\ExStudent\ExStudent;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Domain\Model\ExStudent\MissingExStudent;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;



class ExStudentRepository extends Repository
{
    /**
     * Finds an ex-student by its identifier.
     *
     * @param integer $exStudentId An ex-student's identifier.
     * @return ExStudent The ex-student.
     */
    public function findById(int $exStudentId): ExStudent
    {
        return $this->selectMatch(Criteria::equal('id', $exStudentId)) ?? new MissingExStudent;
    }

    /**
     * Finds an ex-student by its email address.
     *
     * @param integer $email The email address of an ex-student.
     * @return ExStudent The ex-student.
     */
    public function findByEmailAddress(string $email): ExStudent
    {
        return $this->selectMatch(Criteria::equal('email', $email)) ?? new MissingExStudent;
    }

    /**
     * Selects the first ex-student matching a given criteria.
     *
     * @param Criteria $criteria A selection criteria.
     * @return ExStudent The ex-student.
     */
    public function selectMatch(CriteriaContract $criteria): ExStudent
    {
        return $this->strategy->selectMatching($criteria, $this)[0] ?? new MissingExStudent;
    }

    /**
     * Selects ex-students matching a given criteria.
     *
     * @param Criteria $criteria A selection criteria.
     * @return ExStudent[] A collection of ex-students.
     */
    public function selectMatching(CriteriaContract $criteria): array
    {
        return $this->strategy->selectMatching($criteria, $this);
    }

    /**
     * Adds an ex-student to the repository.
     *
     * @param ExStudent $exstudent The ex-student.
     */
    public function add(ExStudent $exstudent)
    {
        $this->strategy->add($exstudent);
    }

    /**
     * Removes an ex-student from the repository.
     *
     * @param ExStudent $exstudent The ex-student.
     */
    public function remove(ExStudent $exstudent)
    {
        $this->strategy->remove($exstudent);
    }

    /**
     * Retrieves all ex-students.
     * 
     * @return ExStudent[] All ex-students.
     */
    public function all(): array
    {
        return $this->strategy->all($this);
    }

    public function getEntityClass(): string
    {
        return MapperRegistry::getMapper(ExStudent::class)->getDataMap()->getEntityClass();
    }
}
