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
     * Category manager instance
     * 
     * @var CategoryManager
     */
    private $CategoryManager;

    public function __construct(Connector $connector, CategoryManager $CategoryManager = null)
    {
        $this->setConnector($connector);
        $this->CategoryManager = $CategoryManager ?? new CategoryManager($connector);
    }

    use ConnectionTrait;

    public function getCategoryManager()
    {
        return $this->CategoryManager;
    }

    public function save(RequestInterface $request)
    {
        $content = (string) $request->getBody();
        $params = json_decode($content);
        $action = $params->action;
        $title = $params->title;
        $categories = $params->categories;
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
        $has_categorized = $this->categorize($categories, $ID);
        if (!$has_categorized) {
            throw new \RuntimeException("Could not categorize the article");
        }
        return $ID;
    }

    protected function categorize(string|array $categories, int $newsID)
    {
        $res = true;
        $categoriesID = [];
        if (is_array($categories)) {
            $input_categories = $categories;
        } else {
            $input_categories = explode(",", $categories);
        }
        // Obtaining the ID of categories from their name if they exist
        $categoriesID = $this->CategoryManager->validate($input_categories);
        foreach ($categoriesID as $categoryID) {
            $sql = "INSERT INTO news_categories VALUES ($newsID, $categoryID)";
            if ($q = $this->connector->getConnection()->query($sql)) {
                $res = $res && boolval($q);
            }
        }
        return $res;
    }

    public function modify(int $ID, array $changes)
    {
        $keys = array_keys($changes);
        $sql = "UPDATE " . self::TABLE . " SET";
        foreach ($keys as $key) {
            if ($key == 'categories') {
                continue;
            }
            $sql .= " $key = :$key,";
        }
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE ID='$ID'";
        $stmt = $this->connector->getConnection()->prepare($sql);
        // If categories are also changed
        if (isset($changes['categories'])) {
            $this->categorize($changes['categories'], $ID);
        }
        $has_modified = $stmt->execute($changes);
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
        $sql = "UPDATE " . self::TABLE . " SET published = '1' WHERE ID = $ID";
        $has_published = $this->connector->getConnection()->query($sql);
        return (bool) $has_published;
    }

    /**
     * @return NewsInterface[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $news_articles = array();
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
        $body = substr($article->getBody(), 0, 180);
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
