<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Mocks\Connection;
use PHPUnit\Framework\TestCase;
use Application\Membership\NewsletterAgent;
use Application\PHPMailerAdapter;

class NewsletterAgentTest extends TestCase
{

    /** @test */
    public function it_broadcast_all()
    {
        $body = "Hey this is a fake mail";
        $mailer = $this->createStub(PHPMailerAdapter::class);

        $mailer->method('send')->willReturn(true);
        $agent = new NewsletterAgent(new Connection(), $mailer);
        $has_sent_all = $agent->broadcast("Fake test mail", $body);
        $this->assertTrue($has_sent_all);
    }

    /** @test */
    public function it_broadcasts_some()
    {
        $body = "Hey this is a fake mail";
        $mailer = $this->createStub(PHPMailerAdapter::class);

        $mailer->method('send')->willReturnOnConsecutiveCalls(true, false, true, true);
        $agent = new NewsletterAgent(new Connection(), $mailer);
        $has_sent_all = $agent->broadcast("Fake test mail", $body);
        $this->assertFalse($has_sent_all);
    }
}
