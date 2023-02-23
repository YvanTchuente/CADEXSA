<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Persistence\Contracts\IdGenerator;

class MysqlIdGenerator implements IdGenerator
{
    public function generateId()
    {
        $stmt = "SELECT UUID_SHORT() as ID";
        $rs = app()->database->getConnection()->pdo->query($stmt);
        $nextId = $rs->fetch()['ID'];
        return $nextId;
    }
}
