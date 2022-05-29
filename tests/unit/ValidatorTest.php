<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\MiddleWare\Request;
use Application\Membership\Validator;
use Application\MiddleWare\TextStream;

class ValidatorTest extends TestCase
{
    public function testValidateCountry()
    {
        // Given a valid country
        $testCountry = 'Cameroon';

        // Assert that it is a valid country
        $is_valid = Validator::validateCountry($testCountry);
        $this->assertTrue($is_valid);

        // Given a invalid country
        $testCountry = 'Gondonia';

        // Assert that it is an invalid country
        $is_valid = Validator::validateCountry($testCountry);
        $this->assertFalse($is_valid);
    }

    public function testValidatePassword()
    {
        // Given a valid password
        $testPassword = 'GoodPassword';
        $is_valid = Validator::validatePassword($testPassword);

        // Assert that it is a valid password
        $this->assertTrue($is_valid);

        // Given a invalid password
        $testPassword = 'wrong';

        // Assert that it is an invalid password
        $is_valid = Validator::validatePassword($testPassword);
        $this->assertFalse($is_valid);
    }

    public function testValidatePhone()
    {
        // Given a valid phone number
        $testPhoneNumber = '657384876';
        $is_valid = Validator::validatePhone($testPhoneNumber);

        // Assert that it is a valid phone number
        $this->assertTrue($is_valid);

        // Given a invalid phone number
        $testPhoneNumber = '66468';

        // Assert that it is an invalid phone number
        $is_valid = Validator::validatePhone($testPhoneNumber);
        $this->assertFalse($is_valid);
    }

    public function testValidateEmail()
    {
        // Given a valid email address
        $testEmail = 'yvantchuente@gmail.com';
        $is_valid = Validator::validateEmail($testEmail);

        // Assert that it is a valid email address
        $this->assertTrue($is_valid);

        // Given a invalid email address
        $testEmail = 'yvantchuente';

        // Assert that it is an invalid email address
        $is_valid = Validator::validateEmail($testEmail);
        $this->assertFalse($is_valid);
    }

    /**
     * @param array payload
     * @dataProvider payloadProvider
     */
    public function testValidateRegistration(array $payload)
    {
        $label = $payload['label'];
        unset($payload['label']);
        if ($label == 'valid') {
            // Given a valid regsitration request
            $stream = new TextStream(json_encode($payload));
            $request = new Request(body: $stream);
            $response = Validator::validateRegistration($request);

            // Assert that it is a registration valid request
            $is_valid = is_array($response);
            $this->assertTrue($is_valid);
        } else {
            // Given a invalid registered request
            $stream = new TextStream(json_encode($payload));
            $request = new Request(body: $stream);
            $response = Validator::validateRegistration($request);

            // Assert that it is an invalid registration request
            $is_valid = is_array($response);
            $this->assertFalse($is_valid);
        }
    }

    public function payloadProvider()
    {
        $payload1 = array(
            'label' => 'valid',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'password' => '20010309',
            'main_contact' => '657384876',
            'secondary_contact' => '657385546',
            'confirm-password' => '20010309'
        );
        $payload2 = array(
            'label' => 'invalid',
            'email' => '',
            'country' => 'Cameroon',
            'password' => '20010309',
            'main_contact' => '657384876',
            'secondary_contact' => '675618485',
            'confirm-password' => '20010309'
        );
        $payload3 = array(
            'label' => 'invalid',
            'email' => 'yvantchuente@gmail.com',
            'country' => '',
            'password' => '20010309',
            'main_contact' => '657384876',
            'secondary_contact' => '675618485',
            'confirm-password' => '20010309'
        );
        $payload4 = array(
            'label' => 'invalid',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'password' => '',
            'main_contact' => '657384876',
            'secondary_contact' => '675618485',
            'confirm-password' => '20010309'
        );
        $payload5 = array(
            'label' => 'invalid',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'password' => '20010309',
            'main_contact' => '657384876',
            'secondary_contact' => '64564825',
            'confirm-password' => '20010308'
        );
        $payload6 = array(
            'label' => 'invalid',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'password' => '20010309',
            'main_contact' => '',
            'secondary_contact' => '645564825',
            'confirm-password' => '20010309',
        );
        $payload7 = array(
            'label' => 'invalid',
            'email' => 'yvantchuente@gmail.com',
            'country' => 'Cameroon',
            'password' => '20010309',
            'main_contact' => '657384876',
            'secondary_contact' => '657385546',
            'confirm-password' => '20010308'
        );
        $payloads = array(
            array('payload' => $payload1),
            array('payload' => $payload2),
            array('payload' => $payload3),
            array('payload' => $payload4),
            array('payload' => $payload5),
            array('payload' => $payload6),
            array('payload' => $payload7)
        );
        return $payloads;
    }
}
