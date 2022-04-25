<?php

declare(strict_types=1);

namespace Application\CMS\Events;

use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\CMS\CMSManager;
use Application\CMS\EventInterface;
use Application\CMS\DeleteItemTrait;
use Application\CMS\ItemExistsTrait;
use Psr\Http\Message\RequestInterface;

class EventManager implements ConnectionAware, CMSManager
{
    /**
     * Events database table name
     */
    protected const TABLE = 'events';

    public function __construct(Connector $conn)
    {
        $this->setConnector($conn);
    }

    use ConnectionTrait;

    public function save(RequestInterface $request)
    {
        $content = (string) $request->getBody();
        $params = json_decode($content);
        $title = $params->title;
        $venue = $params->venue;
        $deadline_date = $params->deadline;
        $time = $params->deadline_time;
        $thumbnail = $params->thumbnail;
        $body = $params->body;

        $deadline = implode(" ", [$deadline_date, $time]);

        $fields = ['title', 'description', 'venue', 'thumbnail', 'publication_date', 'deadline'];
        $timestamp = date('Y-m-d H:i:s');
        $values = array(
            'title' => $title,
            'description' => $body,
            'venue' => $venue,
            'thumbnail' => $thumbnail,
            'publication_date' => $timestamp,
            'deadline' => $deadline,
        );

        if (isset($params->publication_date)) {
            if (preg_match('/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/i', $params->publication_date)) {
                $values['publication_date'] = $params->publication_date;
            }
        }

        foreach ($fields as $value) {
            $placeholders[] = ":$value";
        }
        $sql = "INSERT INTO " . self::TABLE . " (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $this->connector->getConnection()->prepare($sql);
        $stmt->execute($values);
        $ID = (int) $this->connector->getConnection()->lastInsertId();
        return $ID;
    }

    public function modify(int $ID, array $changes)
    {
        $keys = array_keys($changes);
        $sql = "UPDATE " . self::TABLE . " SET";
        foreach ($keys as $key) {
            $sql .= " $key = :$key,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE ID='$ID'";
        $stmt = $this->connector->getConnection()->prepare($sql);
        $has_modified = $stmt->execute($changes);
        return $has_modified;
    }


    public function get(int $ID): EventInterface
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'");
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \RuntimeException(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $event = new Event(
            (int) $data['ID'],
            $data['title'],
            $data['description'],
            $data['venue'],
            $data['thumbnail'],
            $data['publication_date'],
            $data['deadline'],
            EventStatus::from((int) $data['has_happened'])
        );
        return $event;
    }

    /**
     * @return EventInterface[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $events = [];
        $sql = "SELECT ID FROM " . self::TABLE;
        if ($sort) {
            $sql .= " ORDER BY publication_date DESC";
        }
        if ($n > 0) {
            $sql .= " LIMIT $n";
        }
        if (isset($offset)) {
            $sql .= " OFFSET $offset";
        }
        $query = $this->connector->getConnection()->query($sql);
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $column) {
                $events[] = $this->get((int)$column);
            }
        }
        return $events;
    }

    public function preview(int $ID)
    {
        $event = $this->get($ID);
        $title = substr($event->getTitle(), 0, 90);
        $body = substr($event->getBody(), 0, 250);
        if (substr($body, -3, 3) !== '</p>') {
            $body .= '</p>';
        }
        $deadline = $event->getDeadlineDate();
        $preview = array('id' => $ID, 'thumbnail' => $event->getThumbnail(), 'title' => $title, 'body' => $body, 'deadline' => $deadline);
        return $preview;
    }

    use ItemExistsTrait;

    use DeleteItemTrait;
}
