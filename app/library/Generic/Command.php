<?php

namespace Application\Generic;

interface Command
{
    /**
     * Executes the command
     *
     * @return bool
     */
    public function execute();

    /**
     * Undoes the effects of an executed command
     *
     * @return bool
     */
    public function undo();
}
