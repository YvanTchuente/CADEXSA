<?php

declare(strict_types=1);

namespace Application\Database;

/**
 * Represents an instance that interacts with a database server
 * 
 * Opens and persists a connection to a database server
 */
interface Connector
{
    /**
     * Retrieves the connection to the database server
     *
     * @return \PDO
     */
    public function getConnection(): \PDO;
}
