<?php

declare(strict_types=1);

namespace Application\CMS;

class Article extends Item implements ArticleInterface
{
    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $body;

    /**
     * @var string|null
     */
    protected $thumbnail;

    public function getTitle()
    {
        return $this->title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function setTitle(string $title)
    {
        if (!$title) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->title = $title;
        return $this;
    }

    public function setBody(string $body)
    {
        if (!$body) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->body = $body;
        return $this;
    }

    public function setThumbnail(string $thumbnail)
    {
        if (!$thumbnail) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->thumbnail = $thumbnail;
        return $this;
    }
}
