<?php

declare(strict_types=1);

namespace Application\CMS\News;

/**
 * Represents a tag used to label news articles
 */
class Tag
{
    /**
     * @var int
     */
    protected $ID;

    /**
     * @var string
     */
    protected $name;

    public function __construct(int $ID, string $name)
    {
        if (!$ID || !$name) {
            throw new \InvalidArgumentException('Some argument(s) is/are empty');
        }
        $this->ID = $ID;
        $this->name = $name;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getName()
    {
        return $this->name;
    }
}
