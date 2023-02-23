<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\News\NewsArticle;

class DeleteNewsState implements Memento
{
    private NewsArticle $newsArticle;

    private string $date;

    private string $originator;

    public function __construct(DeleteNewsCommand $originator, NewsArticle $newsArticle)
    {
        $this->newsArticle = $newsArticle;
        $this->date = date("Y-m-d H:i:s");
        $this->originator = get_class($originator);
    }

    public function originator()
    {
        return $this->originator;
    }

    public function getName()
    {
        return $this->newsArticle->getTitle();
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getState()
    {
        return $this->newsArticle;
    }
}
