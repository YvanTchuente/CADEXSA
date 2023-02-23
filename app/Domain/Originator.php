<?php

namespace Cadexsa\Domain;

/**
 * Saves its current state into a memento.
 */
interface Originator
{
    /**
     * Saves a snapshot of the current state to memento.
     * 
     * @return Memento The memento. 
     */
    public function saveToMemento(): Memento;

    /**
     * Restores the instance to a saved state.
     * 
     * @param Memento $memento A memento storing a previously saved snapshot of its internal state.
     */
    public function restore(Memento $memento);
}
