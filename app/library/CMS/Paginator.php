<?php

declare(strict_types=1);

namespace Application\CMS;

use Application\CMS\Manager;

/**
 * Separates items managed by a manager into batches for front-end display
 */
class Paginator
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var int 
     */
    protected $items_per_page;

    /**
     * @var int
     */
    protected $total_number_of_pages;

    /**
     * @param int $itemsPerPage Number of items to display per page
     */
    public function __construct(Manager $manager, int $itemsPerPage)
    {
        $this->manager = $manager;
        $this->items_per_page = $itemsPerPage;
        $this->setTotalNumberOfPages();
    }

    /**
     * Returns the total number of batches
     *
     * @return int
     */
    public function getTotalNumberOfPages()
    {
        return $this->total_number_of_pages;
    }

    /**
     * Returns the batch of items for a given page
     * 
     * @param int $page Pagination number of the page
     * 
     * @return array
     * 
     * @throws \DomainException If the page number passed if greater than the total number of pages
     */
    public function paginate(int $page)
    {
        if ($this->total_number_of_pages !== 0 && $page > $this->total_number_of_pages) {
            throw new \DomainException("Page number cannot be greater than the number of pages");
        }
        $offset = ($page - 1) * $this->items_per_page;
        $results = [];
        $all_items = $this->manager->list($this->items_per_page, $offset);
        if (count($all_items) > 0) {
            foreach ($all_items as $item) {
                $publicationDate = $item->getPublicationDate();
                if (!empty($publicationDate)) {
                    $results[] = $item;
                } else {
                    continue;
                }
            }
        }
        return $results;
    }

    protected function setTotalNumberOfPages()
    {
        $all_items = $this->manager->list();
        $published_items = [];
        if (count($all_items) > 0) {
            foreach ($all_items as $item) {
                $publicationDate = $item->getPublicationDate();
                if (!empty($publicationDate)) {
                    $published_items[] = $item;
                } else {
                    continue;
                }
            }
            $total_of_items = count($published_items);
            $number_of_pages = ceil($total_of_items / $this->items_per_page);
        } else {
            $number_of_pages = 0;
        }
        $this->total_number_of_pages = (int) $number_of_pages;
    }
}
