<?php

declare(strict_types=1);

namespace Application\Membership;

interface MemberBuilderInterface
{
    /**
     * @return static 
     */
    public function setMemberID(int $id);

    /**
     * @return static 
     */
    public function setMemberFirstName(string $firstname);

    /**
     * @return static 
     */
    public function setMemberLastName(string $lastname);

    /**
     * @return static 
     */
    public function setMemberUserName(string $username);

    /**
     * @return static 
     */
    public function setMemberEmail(string $email);

    /**
     * @return static 
     */
    public function setMemberContacts(array $contacts);

    /**
     * @return static 
     */
    public function setMemberCountry(string $country);

    /**
     * @return static 
     */
    public function setMemberCity(string $city);

    /**
     * @return static 
     */
    public function setMemberBatch(string $batchYear);

    /**
     * @return static 
     */
    public function setMemberOrientation(MemberOrientation|string $orientation);

    /**
     * @return static 
     */
    public function setMemberAboutMe(string $aboutme);

    /**
     * @return static 
     */
    public function setMemberLevel(MemberLevel|int $level);

    /**
     * @return static 
     */
    public function setMemberLastConnection(string $lastConnection);

    /**
     * @return static 
     */
    public function setMemberRegistrationDate(string $registrationDate);

    /**
     * @return static 
     */
    public function setMemberPicture(string $pictureLocation);

    /**
     * @return Member
     */
    public function getMember();
}
