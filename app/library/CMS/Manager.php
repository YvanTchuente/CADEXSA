<?php

declare(strict_types=1);

namespace Application\CMS;

/**
 * Representation of a manager
 */
interface Manager
{
    /**
     * Returns an item
     * 
     * Returns an item by its ID and returns it as an instance of the class of items managed by the manager
     * 
     * @param int $ID ID of the item to retrieve
     * 
     * @throws \RuntimeException If the item identified by the ID does not exists
     */
    public function get(int $ID);

    /**
     * Returns a list of items
     * 
     * Returns a list ot items into an array. The array consists of instances of the class of items managed by the manager 
     * 
     * @param int $n Number of items to retrieve. Defaults to 0 meaning retrieve all items
     * @param int $offset Specify the position of the item from which to start retrieving
     * @param bool $sort When set to true sort out in descending order otherwise sort out in ascending order
     * 
     * @return array
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true);

    /**
     * Verifies that an item exists
     *
     * @param int $ID ID of the item to verify
     * 
     * @return bool
     */
    public function exists(int $ID);
}
