<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Mocks\Connection;
use Application\CMS\Caretaker;
use Application\CMS\News\News;
use PHPUnit\Framework\TestCase;
use Application\CMS\News\NewsManager;
use Application\CMS\News\DeleteNewsState;
use Application\CMS\News\DeleteNewsCommand;

class CaretakerTest extends TestCase
{
    /** @var Caretaker */
    private $caretaker;

    public function setUp(): void
    {
        $originator = new DeleteNewsCommand(new NewsManager(new Connection()));
        $memento = new DeleteNewsState($originator, ['ID' => 1, 'name' => 'Name of the News article', 'item' => (new News())->setTitle("Title of the article"), 'tag' => 'School']);
        $originator = $this->createStub(DeleteNewsCommand::class);
        $originator->method('saveToMemento')->willReturn($memento);
        $caretaker = new Caretaker($originator);
        $this->caretaker = $caretaker;
    }

    public function testGetOriginator()
    {
        $this->assertInstanceOf('\Application\Generic\Originator', $this->caretaker->getOriginator());
    }

    public function testSetOriginator()
    {
        $originator = new DeleteNewsCommand(new NewsManager(new Connection()));
        $this->caretaker->setOriginator($originator);
        $this->assertInstanceOf('\Application\Generic\Originator', $this->caretaker->getOriginator());
    }

    public function testBackup()
    {
        $this->assertTrue($this->caretaker->backup());
        $caretaker = new Caretaker();
        $this->assertFalse($caretaker->backup());
    }

    public function testUndo()
    {
        $this->caretaker->backup();
        $has_undone = $this->caretaker->undo();
        $this->assertTrue($has_undone);

        $this->caretaker->backup();
        $has_undone = $this->caretaker->undo(0);
        $this->assertTrue($has_undone);

        $this->expectException('Exception');
        $this->caretaker->backup();
        $this->caretaker->undo(2);
    }

    public function testUndoThrowsException()
    {
        $this->expectException('Exception');
        $this->caretaker->undo();
    }

    public function testGetLastMemento()
    {
        $this->caretaker->backup();
        $memento = $this->caretaker->getLastMemento();
        $this->assertInstanceOf('\Application\Generic\Memento', $memento);
    }

    public function testGetLastMementoThrowsException()
    {
        $this->expectException('Exception');
        $memento = $this->caretaker->getLastMemento();
    }

    public function testGetHistory()
    {
        $this->caretaker->backup();
        $this->assertContainsOnly('\Application\Generic\Memento', $this->caretaker->getHistory());
    }
}
