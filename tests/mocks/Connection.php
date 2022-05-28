<?php

namespace Tests\Mocks;

use Application\Database\Connector;

class Connection implements Connector
{
    public function getConnection(): \PDO
    {
        return new \PDO("mysql:host=localhost;dbname=test", "root", "");
    }
}
