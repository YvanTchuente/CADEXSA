<?php

declare(strict_types=1);

namespace Application\Membership;

class MemberBuildDirector
{
    private MemberBuilderInterface $builder;

    public function __construct(MemberBuilderInterface $MemberBuilder)
    {
        $this->builder = $MemberBuilder;
    }

    public function construct(array $memberData)
    {
        $this->builder->setMemberID((int) $memberData['ID'])
            ->setMemberFirstName($memberData['firstname'])
            ->setMemberLastName($memberData['lastname'])
            ->setMemberUserName($memberData['username'])
            ->setMemberEmail($memberData['email'])
            ->setMemberCountry($memberData['country'])
            ->setMemberCity($memberData['city'])
            ->setMemberBatch($memberData['batch_year'])
            ->setMemberOrientation($memberData['orientation'])
            ->setMemberAboutMe($memberData['aboutme'])
            ->setMemberLevel((int) $memberData['level']);

        $contacts["main"] = $memberData['main_contact'];
        if (!empty($memberData['secondary_contact'])) {
            $contacts["secondary"] = $memberData['secondary_contact'];
        }
        $this->builder->setMemberContacts($contacts);
        
        if (isset($memberData['last_connection'])) {
            $this->builder->setMemberLastConnection($memberData['last_connection']);
        }

        $this->builder->setMemberRegistrationDate($memberData['registered_on'])
            ->setMemberPicture($memberData['avatar']);

        return $this;
    }
}
