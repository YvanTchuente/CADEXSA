<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\News;

use Cadexsa\Domain\Model\Content;
use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\Persistence;

/**
 * Represents a news article.
 */
class NewsArticle extends Content
{
    /**
     * The news article's title.
     */
    private string $title;

    /**
     * The news article's body.
     */
    private string $body;

    /**
     * The news article's tags.
     * 
     * A semi-colon-separated list of tags.
     */
    private string $tags;

    /**
     * The URI of the news article's representative image.
     */
    private string $image;

    /**
     * The news article's status.
     */
    private Status $status;

    /**
     * The author's identifier.
     */
    private int $authorId;

    public function __construct(int $id, string $title, string $body, array $tags, string $image, int $authorId, Status $status = Status::UNPUBLISHED, string $publicationDate = null)
    {
        parent::__construct($id, $publicationDate);
        $this->setTitle($title);
        $this->setBody($body);
        $this->setTags(...$tags);
        $this->setImage($image);
        $this->setStatus($status);
        $this->setAuthor($authorId);
    }

    /**
     * Retrieves the news article's title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Retrieves the news article's body.
     */
    public function getBody(bool $summmarize = false)
    {
        if ($summmarize) {
            return strip_tags(substr($this->getBody(),  0, 250));
        } else {
            return $this->body;
        }
    }

    /**
     * Retrieves the news article's representative image.
     * 
     * @return string The URI of the image.
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Retrieves the news article's tags.
     * 
     * @return Tag[]
     */
    public function getTags(): array
    {
        foreach (preg_split("/;\s?/", $this->tags) as $tag) {
            $tags[] = Tag::from($tag);
        }
        return $tags;
    }

    /**
     * Retrieves the news article's author.
     */
    public function getAuthor()
    {
        return Persistence::exStudentRepository()->findById($this->authorId);
    }

    /**
     * Retrieves the news article's status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Determines whether or not the news article is published
     */
    public function published()
    {
        return $this->status == Status::PUBLISHED;
    }

    /**
     * Gets the time elapsed since publication time
     */
    public function getTimeSincePublication()
    {
        return ServiceRegistry::timeIntervalCalculator()->interval($this->getPublicationDate(), new \DateTime);
    }

    /**
     * Sets the news article's title.
     *
     * @param string $title A case-sentitive title.
     */
    public function setTitle(string $title)
    {
        if (!$title) {
            throw new \LengthException("The title is required.");
        }
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the news article's body.
     */
    public function setBody(string $body)
    {
        if (!$body) {
            throw new \LengthException("The body is empty.");
        }
        $this->body = $body;

        return $this;
    }

    /**
     * Sets the news article's representative image.
     *
     * @param string $uri The URI of the image.
     */
    public function setImage(string $uri)
    {
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \DomainException("$uri is not a valid URI.");
        }
        $this->image = $uri;

        return $this;
    }

    /**
     * Sets the news article's tags.
     */
    public function setTags(Tag ...$tags)
    {
        foreach ($tags as $tag) {
            $tagList[] = $tag->value;
        }
        $this->tags = implode("; ", $tagList);

        return $this;
    }

    /**
     * Sets the news article's author.
     *
     * @param int $authorId The author's identifier.
     */
    public function setAuthor(int $authorId)
    {
        if (!$authorId) {
            throw new \DomainException("The identifier must be a non-zero number.");
        }
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * Sets the news article's status.
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }
}
