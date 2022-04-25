<?php

declare(strict_types=1);

namespace Application\CMS;

use Psr\Http\Message\RequestInterface;
use Application\CMS\News\CategoryManager;

class NewsChangeDetector
{
    protected const TABLE = 'news';
    
    /**
     * ID of the news article
     * 
     * @var int
     */
    protected $articleID;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CMSManager
     */
    protected $NewsManager;

    /**
     * @var CategoryManager
     */
    protected $CategoryManager;

    public function __construct(RequestInterface $request, int $articleID, CMSManager $NewsManager, CategoryManager $CategoryManager)
    {
        $this->request = $request;
        $this->articleID = $articleID;
        $this->NewsManager = $NewsManager;
        $this->CategoryManager = $CategoryManager;
    }

    public function detect()
    {
        // Data received from the request
        $params = json_decode($this->request->getBody()->getContents());
        $req_title = $params->title;
        $req_body = $params->body;
        $req_categories = explode(",", $params->categories);

        // Data from the database to compare with for changes
        $article = $this->NewsManager->get($this->articleID);
        $title = $article->getTitle();
        $categories = $this->CategoryManager->getCategory($article);
        foreach ($categories as $category) $categories_names[] = $category->getName();
        $body = $article->getBody();

        if ($title !== $req_title) {
            $changes['title'] = $req_title;
        }
        if ($body !== $req_body) {
            $changes['body'] = $req_body;
        }
        foreach ($req_categories as $req_category) {
            $req_category = trim($req_category);
            $categories = implode(", ", $categories_names);
            if (preg_match("/$req_category/i", $categories)) {
                continue;
            } else {
                $changes['categories'] = $req_category;
            }
        }
        if (isset($changes)) {
            return $changes;
        } else {
            return false;
        }
    }
}
