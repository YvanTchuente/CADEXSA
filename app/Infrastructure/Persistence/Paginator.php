<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

/**
 * Paginates a set of entities.
 */
class Paginator
{
    /**
     * The set of items to paginate.
     *
     * @var Entity[]
     */
    private array $items;

    /**
     * The length of each batch.
     */
    private int $length;

    /**
     * The number of batches.
     */
    public readonly int $batchCount;

    /**
     * @param Entity[] $items The set of entities to paginate.
     * @param integer $length The length of each batch.
     */
    public function __construct(array $items, int $length)
    {
        if (!$items) {
            throw new \LengthException("The set is empty.");
        }
        if (!$length) {
            throw new \DomainException("The length must be a non-zero number.");
        }
        $this->items = $items;
        $this->length = $length;
        $this->batchCount = (int) ceil(count($items) / $this->length);
    }

    /**
     * Retrieves a batch.
     * 
     * @throws \RangeException
     */
    public function getBatch(int $batch_number): array
    {
        if ($batch_number > $this->batchCount) {
            throw new \RangeException("There is no batch at this number.");
        }
        $offset = ($batch_number - 1) * $this->length;
        $batch = array_slice($this->items, $offset, $this->length);
        return $batch;
    }
}
