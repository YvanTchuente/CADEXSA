<?php

namespace Application\CMS\News;

use Application\Generic\Command;
use Application\Generic\Memento;
use Application\CMS\NewsInterface;
use Application\Generic\Originator;
use Application\MiddleWare\Request;
use Application\MiddleWare\TextStream;

class DeleteNewsCommand implements Command, Originator
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

    public function saveToMemento(): memento
    {
        $state = array('ID' => $this->ID, 'item' => $this->item, 'tag' => $this->tag);
        $originator = clone $this;
        $originator->clear(); // Clears all data
        $memento = new DeleteNewsState($originator, $state);
        return $memento;
    }

    private function clear()
    {
        unset($this->ID);
        unset($this->item);
        unset($this->tag);
    }

    public function restore(Memento $m)
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
