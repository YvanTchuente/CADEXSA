<?php

namespace Cadexsa\Domain;

/**
 * Defines a command.
 */
interface Command
{
    /**
     * Executes the command.
     *
     * @throws \RuntimeException if an error occurs.
     */
    public function execute();

    /**
     * Undoes the effect(s) of the previously executed command.
     *
     * @throws \RuntimeException if an error occurs.
     */
    public function undo();
}
