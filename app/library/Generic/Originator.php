<?php

namespace Application\Generic;

/**
 * Describes an instance that saves its current state into a memento
 */
interface Originator
{
    /**
     * Saves the current state into a memento
     * 
     * @return Memento
     */
    public function saveToMemento(): Memento;
    
    /**
     * Restores to the state stored in a memento
     * 
     * @param Memento $m
     */
    public function restore(Memento $m);
}
