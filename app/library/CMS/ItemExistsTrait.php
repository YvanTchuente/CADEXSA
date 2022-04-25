<?php

namespace Application\CMS;

/**
 * Implements the 'exists' method used by manager classes
 */
trait ItemExistsTrait
{
    public function exists(int $ID)
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'");
        $item_exists = (bool) $query->fetch(\PDO::FETCH_ASSOC);
        return $item_exists;
    }
}
