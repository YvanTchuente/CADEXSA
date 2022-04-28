<?php

namespace Application\CMS\News;

use Application\Generic\Command;
use Application\CMS\NewsInterface;
use Application\MiddleWare\Request;
use Application\MiddleWare\TextStream;

class DeleteNewsCommand implements Command
{
    /**
     * @var NewsManager
     */
    protected $NewsManager;

    /**
     * @var int 
     */
    protected $ID;

    /**
     * @var NewsInterface
     */
    protected $item;

    /**
     * @var string
     */
    protected $tag;

    public function __construct(NewsManager $NewsManager)
    {
        $this->NewsManager = $NewsManager;
    }

    public function setID(int $ID)
    {
        $this->ID = $ID;
        $this->initialize();
    }

    public function createMemento()
    {
        $memento = new DeleteNewsState();
        $state = array('ID' => $this->ID, 'item' => $this->item, 'tag' => $this->tag);
        $memento->setState($state);
        return $memento;
    }

    public function setMemento(DeleteNewsState $m)
    {
        $state = $m->getState();
        $this->ID  = $state['ID'];
        $this->item = $state['item'];
        $this->tag = $state['tag'];
    }

    protected function initialize()
    {
        // Initialize state
        $this->item = $this->NewsManager->get($this->ID);
        $TagManager = $this->NewsManager->getTagManager();
        $tag = ($TagManager->getTag($this->item))->getName();
        $this->tag = $tag;
    }

    public function execute()
    {
        if (empty($this->ID)) {
            throw new \RuntimeException("Error executing command: Article's ID is not set");
        }
        return $this->NewsManager->delete($this->ID);
    }

    public function undo()
    {
        if (!isset($this->item)) {
            throw new \RuntimeException("Error undoing last command: News article object not given");
        }
        if ($this->item->wasPublished()) {
            $action = "publish";
        } else {
            $action = "save";
        }
        $content = array(
            'action' => $action,
            'title' => $this->item->getTitle(),
            'tag' => $this->tag,
            'body' => $this->item->getBody(),
            'thumbnail' => $this->item->getThumbnail(),
            'publication_date' => $this->item->getPublicationDate(),
            'authorID' => $this->item->getAuthorID()
        );
        $body = new TextStream(json_encode($content));
        $request = (new Request())->withBody($body);
        return (bool) $this->NewsManager->save($request);
    }
}
