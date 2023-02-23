<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\News;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\NullContentTrait;
use Cadexsa\Domain\Model\ExStudent\MissingExStudent;

class MissingNewsArticle extends NewsArticle implements INull
{
    use NullContentTrait;

    public function getTitle()
    {
        return "Missing article";
    }

    public function getBody()
    {
        return "";
    }

    public function getImage()
    {
        return "";
    }

    public function getAuthor()
    {
        return new MissingExStudent;
    }

    public function createdAt()
    {
        return date('r');
    }

    public function getStatus()
    {
        return Status::UNPUBLISHED;
    }

    public function published()
    {
        return false;
    }

    public function setTitle(string $title)
    {
        return $this;
    }

    public function setBody(string $body)
    {
        return $this;
    }

    public function setImage(string $image)
    {
        return $this;
    }

    public function setAuthor(int $authorId)
    {
        return $this;
    }

    public function setCreatedAt(string $timestamp)
    {
        return $this;
    }

    public function setStatus(Status $status)
    {
        return $this;
    }
}
