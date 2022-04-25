<?php

namespace Application\CMS;

/**
 * Implements the 'delete' method used by manager classes
 */
trait DeleteItemTrait
{
    public function delete(int $ID)
    {
        $exists = $this->exists($ID);
        if (!$exists) {
            throw new \InvalidArgumentException(sprintf("The item referenced by ID of %d does not exit", $ID));
        }
        $sql = "DELETE FROM " . self::TABLE . " WHERE ID = '$ID'";
        $has_deleted = (bool) $this->connector->getConnection()->query($sql);
        return $has_deleted;
    }
}
