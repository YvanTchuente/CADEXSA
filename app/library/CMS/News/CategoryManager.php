<?php

declare(strict_types=1);

namespace Application\CMS\News;

use Application\Database\{
    Connector,
    ConnectionTrait,
    ConnectionAware
};
use Application\CMS\Manager;
use Application\CMS\NewsInterface;
use Application\CMS\ItemExistsTrait;

class CategoryManager implements ConnectionAware, Manager
{
    /**
     * News categories database table name
     */
    protected const TABLE = 'categories';

    /**
     * Name of the databse table housing mappings of news article to their categories
     */
    protected const SECONDARY_TABLE = 'news_categories';

    public function __construct(Connector $connector)
    {
        $this->setConnector($connector);
    }

    use ConnectionTrait;

    public function get(int $ID): Category
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE ID = '$ID";
        $query = $this->connector->getConnection()->query($sql);
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \RuntimeException(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $category = new Category($data['ID'], $data['category']);
        return $category;
    }

    /**
     * Retrieves the categories of an article
     *
     * @param NewsInterface $news_article The news article
     * 
     * @return Category[]
     */
    public function getCategory(NewsInterface $news_article)
    {
        $categories = [];
        $newsID = $news_article->getID();
        $sql = "SELECT categoryID FROM " . self::SECONDARY_TABLE . " WHERE newsID = '$newsID'";
        $query = $this->connector->getConnection()->query($sql);
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $query = $this->connector->getConnection()->query("SELECT * FROM categories WHERE ID = '" . $row['categoryID'] . "'");
            $data = $query->fetch(\PDO::FETCH_ASSOC);
            $category = new Category((int)$data['ID'], $data['category']);
            $categories[] = $category;
        }
        return $categories;
    }

    /**
     * Retrieves the articles categorized with a given category
     *
     * @param Category $category
     * @param NewsManager $NewsManager
     * 
     * @return \Application\CMS\NewsInterface[]
     */
    public function getArticles(Category $category, NewsManager $NewsManager)
    {
        $catID = $category->getID();
        $sql = "SELECT newsID FROM " . self::SECONDARY_TABLE . " WHERE categoryID = '$catID'";
        $query = $this->connector->getConnection()->query($sql);
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $articles[] = $NewsManager->get((int)$row['newsID']);
        }
        return $articles;
    }

    /**
     * Verifies that a category exists
     * 
     * Verifies the existence of a category. If the category exist it will return its ID or false otherwise
     *
     * @param string $category The category's name
     * 
     * @return array|int
     */
    public function validate(array|string $input_category)
    {
        $type = gettype($input_category);
        $registered_categories = $this->list();
        switch ($type) {
            case 'array':
                foreach ($registered_categories as $registered_category) {
                    $name = $registered_category->getName();
                    foreach ($input_category as $category) {
                        $category_name = trim($category);
                        if (preg_match("/$name/i", $category_name)) {
                            $ID = $registered_category->getID();
                            $categoryID[] = $ID;
                            return $categoryID;
                        }
                    }
                }
                break;
            case 'string':
                foreach ($registered_categories as $registered_category) {
                    $name = $registered_category->getName();
                    $category_name = trim($input_category);
                    if (preg_match("/$name/i", $category_name)) {
                        $ID = $registered_category->getID();
                        $categoryID = $ID;
                        return $categoryID;
                    }
                }
                break;
        }
    }

    /**
     * @return Category[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $categories = [];
        $sql = "SELECT * FROM " . self::TABLE;
        if ($n > 1) {
            $sql .= " LIMIT $n";
        }
        if (isset($offset)) {
            $sql .= " OFFSET $offset";
        }
        $query = $this->connector->getConnection()->query($sql);
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $category = new Category((int)$row['ID'], $row['category']);
            $categories[] = $category;
        }
        return $categories;
    }

    use ItemExistsTrait;
}
