<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Transaction;

use Cadexsa\Domain\Model\Entity;

class TransactionManager
{
    private static ?Transaction $currentTransaction;

    /**
     * Initiates a business transaction.
     * 
     * @return Transaction
     */
    public static function beginTransaction()
    {
        self::$currentTransaction = new Transaction;
        return self::$currentTransaction;
    }

    /**
     * Registers an entity as new with the current business transaction.
     * 
     * @throws \RuntimeException If there is no active transaction.
     */
    public static function new(Entity &$entity)
    {
        self::assertTransactionIsActive();
        self::$currentTransaction->new($entity);
    }

    /**
     * Registers an entity as modified with the current business transaction.
     * 
     * @throws \RuntimeException If there is no active transaction.
     */
    public static function dirty(Entity &$entity)
    {
        self::assertTransactionIsActive();
        self::$currentTransaction->dirty($entity);
    }

    /**
     * Registers an entity as deleted with the current business transaction.
     * 
     * @throws \RuntimeException If there is no active transaction.
     */
    public static function deleted(Entity &$entity)
    {
        self::assertTransactionIsActive();
        self::$currentTransaction->deleted($entity);
    }

    /**
     * Commits a business transaction.
     * 
     * Commits the changes made in the transaction and ends the transaction
     * 
     * @throws \RuntimeException If there is no active transaction.
     */
    public static function commit()
    {
        self::assertTransactionIsActive();
        try {
            self::$currentTransaction->commit();
        } finally {
            self::$currentTransaction = null;
        }
    }

    /**
     * Gets the current business transaction.
     * 
     * @return Transaction The business transaction.
     * 
     * @throws \RuntimeException If there is no active transaction.
     */
    public static function getCurrentTransaction(): Transaction
    {
        self::assertTransactionIsActive();
        return self::$currentTransaction;
    }

    /**
     * Sets the current business transaction.
     */
    public static function setCurrentTransaction(Transaction $transaction)
    {
        self::$currentTransaction = $transaction;
    }

    private static function assertTransactionIsActive()
    {
        if (!isset(self::$currentTransaction)) {
            throw new \RuntimeException("There is no active transaction.");
        }
    }
}
