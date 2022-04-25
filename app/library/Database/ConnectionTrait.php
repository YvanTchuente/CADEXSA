<?php

declare(strict_types=1);

namespace Application\Database;

trait ConnectionTrait
{
    /** 
     * The connector instance
     * 
     * @var Connector
     */
    protected Connector $connector;

    public function setConnector(Connector $connector)
    {
        $this->connector = $connector;
    }
}
