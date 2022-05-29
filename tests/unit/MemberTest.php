<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\Membership\Member;
use Application\Membership\MemberLevel;
use Application\Membership\MemberOrientation;

class MemberTest extends TestCase
{
    /** @var Member */
    private $member;

    public function setUp(): void
    {
        $this->member = new Member();
        $this->member->setID(1)
            ->setFirstName('Yvan')
            ->setLastName('Tchuente')
            ->setUserName('webmaster')
            ->setEmail('admin@cadexsa.com')
            ->setContact(['main' => 657384876, 'secondary' => 679055847])
            ->setCountry('Cameroon')
            ->setCity('Douala')
            ->setBatch('2019')
            ->setLevel(MemberLevel::from(1))
            ->setOrientation(MemberOrientation::from('Science'))
            ->setAboutMe('Hey, My name is Yvan')
            ->setPicture("/static/images/gallery/56asd56assd56f.jpg")
            ->setLastConnection('2022-05-02 12:11:50')
            ->setRegistrationDate('2021-11-10 14:20:15');
    }

    // Testing setters
    public function testSetID()
    {
        $this->member->setID(1);
        $this->assertSame(1, $this->member->getID());

        $this->expectException('InvalidArgumentException');
        $this->member->setID(0);
    }

    public function testSetFirstName()
    {
        $this->member->setFirstName('Yvan');
        $this->assertSame('Yvan', $this->member->getFirstName());

        $this->expectException('InvalidArgumentException');
        $this->member->setFirstName('');
    }

    public function testSetLastName()
    {
        $this->member->setLastName('Tchuente');
        $this->assertSame('Tchuente', $this->member->getLastName());

        $this->expectException('InvalidArgumentException');
        $this->member->setLastName('');
    }

    public function testSetUserName()
    {
        $this->member->setUserName('webmaster');
        $this->assertSame('webmaster', $this->member->getUserName());

        $this->expectException('InvalidArgumentException');
        $this->member->setUserName('');
    }

    public function testSetEmail()
    {
        $this->member->setEmail('yvantchuente@gmail.com');
        $this->assertSame('yvantchuente@gmail.com', $this->member->getEmail());

        $this->expectException('InvalidArgumentException');
        $this->member->setEmail('');
    }

    public function testSetContact()
    {
        $contacts = ['main' => 657384876, 'secondary' => 679055847];
        $this->member->setContact($contacts);
        $this->assertSame($contacts['main'], $this->member->getContact('main'));
        $this->assertSame($contacts['secondary'], $this->member->getContact('secondary'));

        $contacts = [];
        $this->expectException('InvalidArgumentException');
        $this->member->setContact($contacts);
    }

    public function testSetCountry()
    {
        $this->member->setCountry('Cameroon');
        $this->assertSame('Cameroon', $this->member->getCountry());

        $this->expectException('InvalidArgumentException');
        $this->member->setCountry('');
    }

    public function testSetCity()
    {
        $this->member->setCity('Douala');
        $this->assertSame('Douala', $this->member->getCity());

        $this->expectException('InvalidArgumentException');
        $this->member->setCity('');
    }

    public function testSetBatch()
    {
        $this->member->SetBatch('2019');
        $this->assertSame('2019', $this->member->GetBatch());

        $this->expectException('InvalidArgumentException');
        $this->member->setBatch("");
    }

    public function testSetOrientation()
    {
        $this->member->setOrientation(MemberOrientation::from('Science'));
        $this->assertSame('Science', $this->member->getOrientation());
    }

    public function testSetAboutMe()
    {
        $this->member->setAboutMe('Hey, My name is yvan');
        $this->assertSame('Hey, My name is yvan', $this->member->getAboutMe());

        $this->expectException('InvalidArgumentException');
        $this->member->setAboutMe("");
    }

    public function testSetLevel()
    {
        $level = MemberLevel::from(1);
        $this->member->setLevel($level);
        $this->assertSame("Administrator", $this->member->getLevel());
    }

    public function testSetPicture()
    {
        $this->member->setPicture("/static/images/gallery/56asd56assd56f.jpg");
        $this->assertSame("/static/images/gallery/56asd56assd56f.jpg", $this->member->getPicture());

        $this->expectException('InvalidArgumentException');
        $this->member->setPicture("");
    }

    public function testSetLastConnection()
    {
        $this->member->setLastConnection('2022-05-02 12:11:50');
        $this->assertSame('2022-05-02 12:11:50', $this->member->getLastConnection());

        $this->expectException('InvalidArgumentException');
        $this->member->setLastConnection("");
    }

    public function testSetRegistrationDate()
    {
        $this->member->setRegistrationDate('2021-11-10 14:20:15');
        $this->assertSame('2021-11-10 14:20:15', $this->member->getRegistrationDate());

        $this->expectException('InvalidArgumentException');
        $this->member->setRegistrationDate("");
    }

    public function testGetID()
    {
        $this->assertSame(1, $this->member->getID());
    }

    public function testGetFistName()
    {
        $this->assertSame('Yvan', $this->member->getFirstName());
    }

    public function testGetLastName()
    {
        $this->assertSame('Tchuente', $this->member->getLastName());
    }

    public function testGetName()
    {
        $this->assertSame('Tchuente Yvan', $this->member->getName());
    }

    public function testGetUserName()
    {
        $this->assertSame('webmaster', $this->member->getUserName());
    }

    public function testGetEmail()
    {
        $this->assertSame('admin@cadexsa.com', $this->member->getEmail());
    }

    public function testGetContact()
    {
        $this->assertSame(657384876, $this->member->getContact());
        $this->assertSame(679055847, $this->member->getContact("secondary"));
    }

    public function testGetCountry()
    {
        $this->assertSame('Cameroon', $this->member->getCountry());
    }

    public function testGetCity()
    {
        $this->assertSame('Douala', $this->member->getCity());
    }

    public function testGetBatch()
    {
        $this->assertSame('2019', $this->member->getBatch());
    }

    public function testGetOrientation()
    {
        $this->assertSame('Science', $this->member->getOrientation());
    }

    public function testGetAboutMe()
    {
        $this->assertSame('Hey, My name is Yvan', $this->member->getAboutMe());
    }

    public function testGetLevel()
    {
        $this->assertSame('Administrator', $this->member->getLevel());
    }

    public function testGetRegisterDate()
    {
        $this->assertSame('2021-11-10 14:20:15', $this->member->getRegistrationDate());
    }

    public function testGetLastConnection()
    {
        $this->assertSame('2022-05-02 12:11:50', $this->member->getLastConnection());
    }

    public function testGetPicture()
    {
        $this->assertSame("/static/images/gallery/56asd56assd56f.jpg", $this->member->getPicture());
    }
}
