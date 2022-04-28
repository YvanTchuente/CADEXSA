<?php

declare(strict_types=1);

namespace Application\CMS\News;

use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\CMS\CMSManager;
use Application\CMS\NewsInterface;
use Application\CMS\DeleteItemTrait;
use Application\CMS\ItemExistsTrait;
use Psr\Http\Message\RequestInterface;
use Application\DateTime\TimeDurationInterface;

class NewsManager implements ConnectionAware, CMSManager
{
    /**
     * News database table name
     */
    protected const TABLE = 'news';

    /**
     * @var TagManager
     */
    private $TagManager;

    public function __construct(Connector $connector, TagManager $TagManager = null)
    {
        $this->setConnector($connector);
        $this->TagManager = $TagManager ?? new TagManager($connector);
    }

    use ConnectionTrait;

    public function getTagManager()
    {
        return $this->TagManager;
    }

    public function save(RequestInterface $request)
    {
        $content = (string) $request->getBody();
        $params = json_decode($content);
        $action = $params->action;
        $title = $params->title;
        $tag = $params->tag;
        $body = $params->body;
        $thumbnail = $params->thumbnail;
        $authorID = (int)$params->authorID;

        $fields = ['authorID', 'title', 'body', 'thumbnail', 'published', 'creation_date'];
        $timestamp = date('Y-m-d H:i:s');
        $values = array(
            'authorID' => $authorID,
            'title' => $title,
            'body' => $body,
            'thumbnail' => $thumbnail,
            'published' => '0',
            'creation_date' => $timestamp
        );

        if ($action == 'publish') {
            $fields[] = 'publication_date';
            $values['published'] = '1';
            $values['publication_date'] = $timestamp;
        }

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
        $has_classified = $this->classifyByTag($tag, $ID);
        if (!$has_classified) {
            throw new \RuntimeException("Could not classify the article");
        }
        return $ID;
    }

    protected function classifyByTag(string $tag, int $newsID): bool
    {
        // Obtaining the ID of tag from their name if they exist
        $tagID = $this->TagManager->validate($tag);
        $is_item_existing = $this->exists($newsID);
        $has_succeeded = false;
        if ($is_item_existing) {
            $sql = "SELECT * FROM news_tags WHERE newsID = '$newsID'";
            $res = $this->connector->getConnection()->query($sql);
            if (!$res->fetch()) {
                $sql = "INSERT INTO news_tags (newsID, tagID) VALUES (?, ?)";
                $stmt = $this->connector->getConnection()->prepare($sql);
                $has_succeeded = $stmt->execute([$newsID, $tagID]);
            } else {
                $sql = "UPDATE news_tags SET tagID = ? WHERE newsID = ?";
                $stmt = $this->connector->getConnection()->prepare($sql);
                $has_succeeded = $stmt->execute([$newsID, $tagID]);
            }
        }
        return $has_succeeded;
    }

    public function modify(int $ID, array $changes)
    {
        $has_modified = true;
        if (array_key_exists('tag', $changes)) {
            $has_classified = $this->classifyByTag($changes['tag'], $ID);
            $has_modified = $has_classified and $has_modified;
            unset($changes['tag']);
        }
        if (!empty($changes)) {
            $keys = array_keys($changes);
            $sql = "UPDATE " . self::TABLE . " SET";
            foreach ($keys as $key) {
                $sql .= " $key = :$key,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= " WHERE ID='$ID'";
            $stmt = $this->connector->getConnection()->prepare($sql);
            $has_succeeded = $stmt->execute($changes);
            $has_modified = $has_succeeded and $has_modified;
        }
        return $has_modified;
    }

    /**
     * Marks a news article as being published
     *
     * @param integer $ID
     * 
     * @return bool
     * 
     * @throws \InvalidArgumentException 
     */
    public function publish(int $ID)
    {
        $exists = $this->exists($ID);
        if (!$exists) {
            throw new \InvalidArgumentException(sprintf("The item referenced by ID of %d does not exit", $ID));
        }
        $sql = "UPDATE " . self::TABLE . " SET published = '1', publication_date = current_timestamp() WHERE ID = $ID";
        $has_published = $this->connector->getConnection()->query($sql);
        return (bool) $has_published;
    }

    /**
     * @return NewsInterface[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $news_articles = [];
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
                $news_articles[] = $this->get((int)$column);
            }
        }
        return $news_articles;
    }

    public function get(int $ID): NewsInterface
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'");
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \RuntimeException(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $article = new News(
            (int) $data['ID'],
            (int) $data['authorID'],
            $data['title'],
            $data['body'],
            $data['thumbnail'],
            $data['publication_date'],
            $data['creation_date'],
            NewsStatus::from((int) $data['published'])
        );
        return $article;
    }

    public function preview(int $ID, TimeDurationInterface $TimeDuration)
    {
        $article = $this->get($ID);
        $title = substr($article->getTitle(), 0, 90);
        $body = substr($article->getBody(), 0, 200);
        $publication_date = new \DateTime($article->getPublicationDate());
        $TimeDuration->setReferenceTime($publication_date);
        $TimeDuration->setTargetTime(new \DateTime());
        $duration = $TimeDuration->getLongestDuration();
        $preview = array('id' => $ID, 'thumbnail' => $article->getThumbnail(), 'title' => $title, 'body' => $body, 'timeDiff' => $duration);
        return $preview;
    }

    use ItemExistsTrait;

    use DeleteItemTrait;
}
