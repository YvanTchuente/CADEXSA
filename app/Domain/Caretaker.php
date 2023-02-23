<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Memento;

/**
 * Safeguards mementos.
 */
class Caretaker
{
    /**
     * The history list of mementos.
     * 
     * @var Memento[][]
     */
    private array $history = [];

    /**
     * Backs up the current state of an originator.
     * 
     * @param Originator $originator The originator.
     */
    public function backup(Originator $originator)
    {
        $classname = get_class($originator);
        $this->history[$classname][] = $originator->saveToMemento();
    }

    /**
     * Deletes a memento with a given name.
     *
     * @param string $name The name of a memento.
     */
    public function deleteMemento(string $name)
    {
        foreach ($this->history as $originator => $mementos) {
            foreach ($mementos as $key => $memento) {
                if (preg_match("/$name/", $memento->getName())) {
                    unset($this->history[$originator][$key]);
                    break 2;
                }
            }
        }
        if (!$this->history[$originator]) {
            unset($this->history[$originator]);
        }
    }

    /**
     * Restores an originator to a previous state.
     * 
     * Retrieves a memento from the history list and restores the originator to the state
     * stored in the memento.
     *
     * @param Originator $originator The originator.
     * @param int|null $level [optional] The level of the memento within the history list
     *                        to which the originator should restore its current state to,
     *                        it defaults to the uppermost memento of the list.
     * 
     * @throws \RuntimeException
     */
    public function restore(Originator $originator, ?int $level = null)
    {
        if (empty($this->history[get_class($originator)])) {
            throw new \RuntimeException("The history list is empty");
        }
        if (isset($level)) {
            if (!isset($this->history[get_class($originator)][$level])) {
                throw new \OutOfBoundsException("There is no memento at this level.");
            }
            $memento = $this->history[get_class($originator)][$level];
            array_splice($this->history[get_class($originator)], $level, 1);
            if (!$this->history[get_class($originator)]) {
                unset($this->history[get_class($originator)]);
            }
        } else {
            $memento = array_pop($this->history[get_class($originator)]);
        }
        $originator->restore($memento);
    }

    /**
     * Retrieves the last mememto from the internal history list.
     *
     * @return Memento The retrieved memento.
     * 
     * @throws \UnderflowException If the history list is empty.
     */
    public function getLastMemento(): Memento
    {
        $history = $this->getHistory();
        $length = count($history);
        $lastPosition = ($length > 0) ? $length - 1 : null;
        if (is_null($lastPosition)) {
            throw new \UnderflowException("The history list is empty");
        }
        return $history[$lastPosition];
    }

    /**
     * Retrieves all of the mementos.
     */
    public function getHistory()
    {
        $history = [];
        foreach ($this->history as $mementos) {
            $history = array_merge($history, $mementos);
        }
        return $history;
    }

    public function __sleep()
    {
        return array('history');
    }
}
