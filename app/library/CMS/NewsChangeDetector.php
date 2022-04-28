<?php

declare(strict_types=1);

namespace Application\CMS;

use Application\CMS\News\TagManager;
use Psr\Http\Message\RequestInterface;

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
     * @var TagManager
     */
    protected $TagManager;

    public function __construct(RequestInterface $request, int $articleID, CMSManager $NewsManager, TagManager $TagManager)
    {
        $this->request = $request;
        $this->articleID = $articleID;
        $this->NewsManager = $NewsManager;
        $this->TagManager = $TagManager;
    }

    public function detect()
    {
        // Data received from the request
        $params = json_decode($this->request->getBody()->getContents());
        $req_title = $params->title;
        $req_body = $params->body;
        $req_tag = trim($params->tag);

        // Data from the database to compare with for changes
        $article = $this->NewsManager->get($this->articleID);
        $title = $article->getTitle();
        $tag = $this->TagManager->getTag($article);
        $tagName = $tag->getName();
        $body = $article->getBody();

        if ($title !== $req_title) {
            $changes['title'] = $req_title;
        }
        if ($body !== $req_body) {
            $changes['body'] = $req_body;
        }
        if (!preg_match("/$req_tag/i", $tagName)) {
            $changes['tag'] = $req_tag;
        }
        if (isset($changes)) {
            return $changes;
        } else {
            return false;
        }
    }
}
