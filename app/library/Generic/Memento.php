<?php

namespace Application\Generic;

/**
 * Describes a saved state of an originator
 */
interface Memento
{
    /**
     * Returns the date when the memento was created
     *
     * @return string
     */
    public function getDate();

    /**
     * Returns the name of the memento
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the originator of the memento
     * 
     * @return Originator
     */
    public function getOriginator();
}
