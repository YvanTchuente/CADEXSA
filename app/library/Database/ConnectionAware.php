<?php

declare(strict_types=1);

namespace Application\Database;

/**
 * Describes a database connection-aware instance
 */
interface ConnectionAware
{
    /**
     * Sets a connector instance on the object
     *
     * @param Connector $Connector The connector instance
     */
    public function setConnector(Connector $Connector);
}
