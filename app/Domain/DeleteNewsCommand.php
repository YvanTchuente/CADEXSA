<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\News\NewsArticle;

class DeleteNewsCommand implements Command, Originator
{
    private NewsArticle $newsArticle;

    public function __construct(int $newsArticleId = null)
    {
        if ($newsArticleId) {
            $this->newsArticle = Persistence::newsArticleRepository()->findById($newsArticleId);
        }
    }

    public function saveToMemento(): Memento
    {
        $memento = new DeleteNewsState($this, $this->newsArticle);
        return $memento;
    }

    public function restore(Memento $memento)
    {
        $newsArticle = $memento->getState();
        $this->newsArticle = $newsArticle;
    }

    public function execute()
    {
        Persistence::newsArticleRepository()->remove($this->newsArticle);
    }

    public function undo()
    {
        Persistence::newsArticleRepository()->add($this->newsArticle);
    }
}
