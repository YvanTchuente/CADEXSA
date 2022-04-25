<?php

declare(strict_types=1);

namespace Application\CMS;

use Psr\Http\Message\RequestInterface;

/**
 * Describes a manager in the context of a Content Management System
 */
interface CMSManager extends Manager
{
    /**
     * Saves an item
     * 
     * Examines a client-sent request to save item and saves the item from the data contained in the body of the request
     * and returns the ID of the saved item. The if the payload of the request is textual, it should be JSON encoded.
     * 
     * @return int ID of the saved item
     */
    public function save(RequestInterface $request);

    /**
     * Modifies an item with specific changes
     * 
     * @param int $ID ID of the item to modify
     * @param array $changes An array mapping Database table column(s) to the changes to be applied to the column(s)
     * 
     * @return bool
     */
    public function modify(int $ID, array $changes);

    /**
     * Deletes an item
     * 
     * @param int $ID ID of the item to delete
     * 
     * @return bool
     */
    public function delete(int $ID);
}
