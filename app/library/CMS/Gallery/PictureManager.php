<?php

declare(strict_types=1);

namespace Application\CMS\Gallery;

use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\CMS\Manager;
use Application\CMS\ItemExistsTrait;
use Application\CMS\PictureInterface;

class PictureManager implements ConnectionAware, Manager
{
    /**
     * Gallery pictures database table name
     */
    protected const TABLE = 'gallery_pictures';

    public function __construct(Connector $conn)
    {
        $this->setConnector($conn);
    }

    use ConnectionTrait;

    public function get(int $ID): PictureInterface
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'");
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \RuntimeException(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $picture = new Picture((int) $data['ID'], $data['name'], $data['description'], $data['snapshot_date'], $data['publication_date']);
        return $picture;
    }

    /**
     * @return PictureInterface[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $pictures = [];
        $sql = "SELECT ID FROM " . self::TABLE;
        if ($sort) {
            $sql .= " ORDER BY publication_date DESC";
        }
        if ($n > 1) {
            $sql .= " LIMIT $n";
        }
        if (isset($offset)) {
            $sql .= " OFFSET $offset";
        }
        $query = $this->connector->getConnection()->query($sql);
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $column) {
                $pictures[] = $this->get((int)$column);
            }
        }
        return $pictures;
    }

    use ItemExistsTrait;
}
