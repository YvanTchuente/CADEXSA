<?php

declare(strict_types=1);

namespace Application\CMS\News;

use Exception;
use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\CMS\Manager;
use Application\CMS\NewsInterface;
use Application\CMS\ItemExistsTrait;

class TagManager implements ConnectionAware, Manager
{
    protected const TABLE = 'tags';
    protected const SECONDARY_TABLE = 'news_tags';

    public function __construct(Connector $connector)
    {
        $this->setConnector($connector);
    }

    use ConnectionTrait;

    public function get(int $ID): Tag
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'";
        $query = $this->connector->getConnection()->query($sql);
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new Exception(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $tag = new Tag($data['ID'], $data['tag']);
        return $tag;
    }

    /**
     * Retrieves the tag of a news article
     *
     * @param NewsInterface $article The news article
     * 
     * @return Tag
     */
    public function getTag(NewsInterface $article)
    {
        $newsID = $article->getID();
        // Fetch the tagID of the article
        $sql = "SELECT tagID FROM " . self::SECONDARY_TABLE . " WHERE newsID = '$newsID'";
        $query = $this->connector->getConnection()->query($sql);
        $row = $query->fetch(\PDO::FETCH_ASSOC);
        // Retrieve the tag as an object from the db and return
        $query = $this->connector->getConnection()->query("SELECT * FROM tags WHERE ID = '" . $row['tagID'] . "'");
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        $tag = new Tag((int)$data['ID'], $data['tag']);
        return $tag;
    }

    /**
     * Retrieves the articles tagged with a specific tag
     *
     * @param Tag $Tag The tag
     * @param Manager $NewsManager A NewsManager instance
     * 
     * @return \Application\CMS\NewsInterface[]
     */
    public function getArticles(Tag $Tag, Manager $NewsManager)
    {
        $articles = [];
        $tagID = $Tag->getID();
        $sql = "SELECT newsID FROM " . self::SECONDARY_TABLE . " WHERE tagID = '$tagID'";
        $query = $this->connector->getConnection()->query($sql);
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $articles[] = $NewsManager->get((int)$row['newsID']);
        }
        return $articles;
    }

    /**
     * Verifies that a tag exists
     * 
     * Verifies the existence of a tag. If the tag exist it will return its ID or false otherwise
     *
     * @param string $tag The tag's name
     * 
     * @return int|false
     */
    public function validate(string $tag)
    {
        $registered_tags = $this->list();
        foreach ($registered_tags as $registered_tag) {
            $name = $registered_tag->getName();
            $tag_name = trim($tag);
            if (preg_match("/^$name$/i", $tag_name)) {
                $tagID = $registered_tag->getID();
                return $tagID;
            }
        }
        if (!isset($tagID)) {
            return false;
        }
    }

    /**
     * @return Tag[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $tags = [];
        $sql = "SELECT * FROM " . self::TABLE;
        if ($n > 0) {
            $sql .= " LIMIT $n";
        }
        if (isset($offset)) {
            $sql .= " OFFSET $offset";
        }
        $query = $this->connector->getConnection()->query($sql);
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $Tag = new Tag((int)$row['ID'], $row['tag']);
            $tags[] = $Tag;
        }
        return $tags;
    }

    use ItemExistsTrait;
}
