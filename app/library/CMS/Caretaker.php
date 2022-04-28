<?php

namespace Application\CMS;

use Exception;
use Application\Generic\Memento;
use Application\Generic\Originator;

/**
 * Keeps a list of mementos to be later used by their originators
 */
class Caretaker
{
    /**
     * List of mementos
     * 
     * @var Memento[]
     */
    private $history = [];

    /**
     * @var Originator
     */
    private $originator;

    public function __construct(Originator $originator = null)
    {
        $this->originator = $originator;
    }

    public function getOriginator()
    {
        return $this->originator;
    }

    public function setOriginator(Originator $originator)
    {
        $this->originator = $originator;
        return $this;
    }

    /**
     * Retrieves and save a memento from the wrapped originator
     *
     * @return bool
     */
    public function backup()
    {
        if (empty($this->originator)) {
            return false;
        }
        $this->history[] = $this->originator->saveToMemento();
        return true;
    }

    /**
     * Retrieves a memento from the internal list and restore the originator
     * to the state stored in the memento
     *
     * @param int|null $level The level of the memento within the internal list of stored mementos
     *                        to which the originator should restore its current state to.
     *                        If not provided, it defaults to the uppermost memento in the list
     * 
     * @return bool
     * 
     * @throws \UnderflowException
     */
    public function undo(int $level = null)
    {
        if (empty($this->history)) {
            throw new \UnderflowException("The internal list of mementos is empty");
        }
        if (isset($level)) {
            if (!isset($this->history[$level])) {
                throw new \OutOfBoundsException("No memento is referenced at this level");
            }
            $memento = $this->history[$level];
            $originator = $memento->getOriginator();
            array_splice($this->history, $level, 1);
        } else {
            $memento = array_pop($this->history);
            $originator = $memento->getOriginator();
        }
        $this->originator = $originator;
        $this->originator->restore($memento);
        return true;
    }

    /**
     * Retrieves the last mememto form the internal list of stored memento
     *
     * @return Memento
     * 
     * @throws \UnderflowException
     */
    public function getLastMemento(): Memento
    {
        $length = (!empty($this->history)) ? count($this->history) : 0;
        $last = ($length > 0) ? $length - 1 : null;
        if (is_null($last)) {
            throw new \UnderflowException("The internal list of mementos is empty");
        }
        return $this->history[$last];
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function __sleep()
    {
        return array('history');
    }
}
