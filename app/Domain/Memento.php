<?php

namespace Cadexsa\Domain;

/**
 * Defines a memento.
 * 
 * A memento is an object that stores a snapshot of the internal state of its originator.
 */
interface Memento
{
    /**
     * Retrieves the class name of its originator.
     *
     * @return string
     */
    public function originator();

    /**
     * Retrieves the name assigned to the state stored in the memento.
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieves the date of creation of the memento.
     *
     * @return string
     */
    public function getDate();

    /**
     * Retrieves the stored state.
     *
     * @return mixed The state.
     */
    public function getState();
}
