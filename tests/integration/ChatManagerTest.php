<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Mocks\Connection;
use PHPUnit\Framework\TestCase;
use Application\Security\Securer;
use Application\Membership\Chats\{Chat, ChatManager};

class ChatManagerTest extends TestCase
{
    /** @var Securer */
    private $securer;
    /** @var ChatManager */
    private $ChatManager;

    public static function setUpBeforeClass(): void
    {
        $pdo = (new Connection())->getConnection();
        $pdo->query("DELETE FROM chats");
        $pdo->query("ALTER TABLE chats AUTO_INCREMENT = 1");
    }

    public function setUp(): void
    {
        $this->ChatManager = new ChatManager(new Connection());
        $msg = 'Hey man, how are you doing';
        $securer = $this->createStub(Securer::class);
        $securer->method('encrypt')->willReturn(['cipherText' => 'sdfjgdfajhdgjgasdgjhhgasfd==', 'key' => 'sdfgjhfgja45hhjsdad=', 'iv' => 'sdhfjgajdgaghjfsdf=']);
        $securer->method('decrypt')->willReturn($msg);
        $this->securer = $securer;
    }

    public function testSave()
    {
        $msg = 'Hey man, how are you doing';
        $chat = new Chat(1, 1, 2, $msg, '2022-05-13 08:23:30');
        $ID = $this->ChatManager->save($chat, $this->securer);
        $this->assertSame(1, $ID);
    }

    public function testGet()
    {
        $chat = $this->ChatManager->get(1);
        $this->assertSame(1, $chat->getID());

        $this->expectException('Exception');
        $chat = $this->ChatManager->get(2);
    }

    public function testList()
    {
        $list = $this->ChatManager->list(1);
        $this->assertContainsOnly('\Application\Membership\Chats\Chat', $list);
    }

    /** @test */
    public function it_throws_exception_on_invalid_chat()
    {
        $msg = 'Hey man, how are you doing';
        $chat = new Chat(1, 1, 2, $msg);
        $this->expectException('Exception');
        $this->ChatManager->save($chat, $this->securer);
    }

    public function testGetConversation()
    {
        $msg = 'Hey man, how are you doing';
        $chat = new Chat(1, 1, 2, $msg, '2022-05-13 08:23:30');
        $conversation = $this->ChatManager->getConversation(1, 2, $this->securer);
        $this->assertEquals($chat, $conversation[0]);
    }

    /** @test */
    public function it_throws_exception_on_same_sender_and_receiver()
    {
        $msg = 'Hey man, how are you doing';
        $chat = new Chat(1, 1, 2, $msg, '2022-05-13 08:23:30');
        $this->expectException('InvalidArgumentException');
        $this->ChatManager->getConversation(1, 1, $this->securer);
    }

    public function testExists()
    {
        $ID = 1;
        $exists = $this->ChatManager->exists($ID);
        $this->assertTrue($exists);
    }

    /** @test */
    public function it_throws_exception_on_invalid_ID()
    {
        $ID = 2;
        $this->expectException('InvalidArgumentException');
        $this->ChatManager->delete($ID);
    }

    public function testDelete()
    {
        $ID = 1;
        $has_deleted = $this->ChatManager->delete($ID);
        $this->assertTrue($has_deleted);
    }
}
