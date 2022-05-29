<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\Membership\Chats\Chat;

class ChatTest extends TestCase
{
    /** @var Chat */
    private $chat;

    public function setUp(): void
    {
        $this->chat = new Chat(1, 1, 2, 'Hey Collins', '2022-05-05 20:50:15');
    }

    public function testSetID()
    {
        $this->chat->setID(2);
        $this->assertSame(2, $this->chat->getID());

        $this->expectException('InvalidArgumentException');
        $this->chat->setID(0);
    }

    public function testSetSenderID()
    {
        $this->chat->SetSenderID(2);
        $this->assertSame(2, $this->chat->getSenderID());

        $this->expectException('InvalidArgumentException');
        $this->chat->setSenderID(0);
    }

    public function testSetReceiverID()
    {
        $this->chat->setReceiverID(1);
        $this->assertSame(1, $this->chat->getReceiverID());

        $this->expectException('InvalidArgumentException');
        $this->chat->setReceiverID(0);
    }

    public function testSetMessage()
    {
        $this->chat->setMessage('Hey Yvan');
        $this->assertSame('Hey Yvan', $this->chat->getMessage());

        $this->expectException('InvalidArgumentException');
        $this->chat->setMessage("");
    }

    public function testSetTimestamp()
    {
        $this->chat->setTimestamp("2020-04-05 23:50:15");
        $this->assertSame("2020-04-05 23:50:15", $this->chat->getTimestamp());

        $this->expectException('InvalidArgumentException');
        $this->chat->setTimestamp("");
    }

    /** @test */
    public function it_throws_exception_when_invalid_timestamp_is_passed() {
        $this->expectException('InvalidArgumentException');
        $this->chat->setTimestamp("asd4564afsa");
    }

    public function testGetID()
    {
        $this->assertSame(1, $this->chat->getID());
    }

    public function testGetSenderID()
    {
        $this->assertSame(1, $this->chat->getSenderID());
    }

    public function testGetReceiverID()
    {
        $this->assertSame(2, $this->chat->getReceiverID());
    }

    public function testGetMessage()
    {
        $this->assertSame('Hey Collins', $this->chat->getMessage());
    }

    public function testGetTimestamp()
    {
        $this->assertSame('2022-05-05 20:50:15', $this->chat->getTimestamp());
    }
}
