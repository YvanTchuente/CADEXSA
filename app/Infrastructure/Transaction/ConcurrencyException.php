<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Transaction;

/**
 * Exception that represents a concurrency violation
 */
class ConcurrencyException extends \RuntimeException
{
}
