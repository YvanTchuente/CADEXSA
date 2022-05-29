<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\Membership\Member;
use Application\Membership\MemberLevel;
use Application\Membership\MemberBuilder;
use Application\Membership\MemberOrientation;

class MemberBuilderTest extends TestCase
{
    public function test_it_assembles_member()
    {
        // Given a MemberBuilder
        $MemberBuilder = new MemberBuilder();

        $testMemberArray = [
            'ID' => 1,
            'firstname' => 'Yvan',
            'lastname' => 'Tchuente',
            'username' => 'webmaster',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'city' => 'Douala',
            'batch_year' => '2019',
            'orientation' => 'Science',
            'aboutme' => 'Hey, my name is Yvan',
            'last_connection' => '2022-05-13 5:47:40',
            'registered_on' => '2021-11-10 5:20:15',
            'avatar' => '/profile.jpg',
            'level' => 1,
        ];
        $contacts = [
            'main' => '657384876',
            'secondary' => '679055847'
        ];

        $testMember = new Member();
        $testMember->setID($testMemberArray['ID'])
            ->setFirstName($testMemberArray['firstname'])
            ->setLastName($testMemberArray['lastname'])
            ->setUserName($testMemberArray['username'])
            ->setEmail($testMemberArray['email'])
            ->setContact($contacts)
            ->setCountry($testMemberArray['country'])
            ->setCity($testMemberArray['city'])
            ->setBatch($testMemberArray['batch_year'])
            ->setOrientation(MemberOrientation::from($testMemberArray['orientation']))
            ->setAboutMe($testMemberArray['aboutme'])
            ->setLevel(MemberLevel::from($testMemberArray['level']))
            ->setLastConnection($testMemberArray['last_connection'])
            ->setRegistrationDate($testMemberArray['registered_on'])
            ->setPicture($testMemberArray['avatar']);

        // Build a member using the builder's interface
        $MemberBuilder->setMemberID($testMemberArray['ID'])
            ->setMemberFirstName($testMemberArray['firstname'])
            ->setMemberLastName($testMemberArray['lastname'])
            ->setMemberUserName($testMemberArray['username'])
            ->setMemberEmail($testMemberArray['email'])
            ->setMemberContacts($contacts)
            ->setMemberCountry($testMemberArray['country'])
            ->setMemberCity($testMemberArray['city'])
            ->setMemberBatch($testMemberArray['batch_year'])
            ->setMemberOrientation($testMemberArray['orientation'])
            ->setMemberAboutMe($testMemberArray['aboutme'])
            ->setMemberLevel($testMemberArray['level'])
            ->setMemberLastConnection($testMemberArray['last_connection'])
            ->setMemberRegistrationDate($testMemberArray['registered_on'])
            ->setMemberPicture($testMemberArray['avatar']);

        // Assert that the manually assembled member is the same as the member object constructed using the builder's interface
        $this->assertEquals($testMember, $MemberBuilder->getMember());
    }
}
