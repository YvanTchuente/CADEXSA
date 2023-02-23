<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Picture\Picture;
use Cadexsa\Domain\Model\Picture\MissingPicture;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;


class PictureRepository extends Repository
{
    /**
     * Finds a picture by its identifier.
     *
     * @param integer $pictureId The identifier of a picture.
     * @return Picture The picture.
     */
    public function findById(int $pictureId): Picture
    {
        $criteria = Criteria::equal('id', $pictureId);
        $picture = $this->selectMatch($criteria) ?? new MissingPicture;
        return $picture;
    }

    /**
     * Selects the first picture matching a given criteria.
     * 
     * @param Criteria $criteria A selection criteria.
     * @return Picture The picture.
     */
    public function selectMatch(CriteriaContract $criteria): Picture
    {
        return $this->strategy->selectMatching($criteria, $this)[0] ?? new MissingPicture;
    }

    /**
     * Selects pictures matching a given criteria.
     * 
     * @param Criteria $criteria A selection criteria.
     * 
     * @return Picture[] A collection of pictures.
     */
    public function selectMatching(CriteriaContract $criteria): array
    {
        return $this->strategy->selectMatching($criteria, $this);
    }

    /**
     * Adds a picture to the repository.
     *
     * @param Picture The picture.
     */
    public function add(Picture $picture)
    {
        $this->strategy->add($picture);
    }

    /**
     * Removes a picture from the repository.
     *
     * @param Picture The picture.
     */
    public function remove(Picture $picture)
    {
        $this->strategy->remove($picture);
    }

    /**
     * @return Picture[] All pictures.
     */
    public function all(): array
    {
        return $this->strategy->all($this);
    }

    public function getEntityClass(): string
    {
        return MapperRegistry::getMapper(Picture::class)->getDataMap()->getEntityClass();
    }
}
