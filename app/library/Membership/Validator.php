<?php

declare(strict_types=1);

namespace Application\Membership;

use Application\MiddleWare\Stream;
use Psr\Http\Message\RequestInterface;

class Validator
{
    public static function validateCountry(string $country)
    {
        $stream = new Stream(dirname(__DIR__, 2) . '/static/database/valid_countries.json');
        $valid_countries = (json_decode($stream->getContents()))->Countries;
        foreach ($valid_countries as $valid_country) {
            if ($valid_country == $country) {
                return true;
                break;
            }
        }
        return false;
    }

    public static function validatePassword(string $password)
    {
        if (ctype_alnum($password) && strlen($password) >= 8) {
            return true;
        }
        return false;
    }

    public static function validateEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function validatePhone(string $phoneNumber)
    {
        if (ctype_digit($phoneNumber) && strlen($phoneNumber) == 9) {
            return true;
        }
        return false;
    }

    /**
     * Validates the user input from the member registration form
     *
     * Validates the member registration form's input and return the user's input as an array otherwise
     * return a string containing any invalid form input
     *
     * @param RequestInterface $request The HTTP request emanating from the register form
     * @return array|string
     **/
    public static function validateRegistration(RequestInterface $request)
    {
        $contents = (string) $request->getBody();
        $params = json_decode($contents);
        $iterator = new \ArrayIterator($params);
        foreach ($iterator as $key => $value) {
            $userInfo[$key] = $value;
        }
        if (!self::validateEmail($userInfo['email'])) {
            return "Invalid email address";
        }
        if (!self::validateCountry($userInfo['country'])) {
            return "Invalid country";
        }
        if (!self::validatePhone($userInfo['contact'])) {
            return "Invalid phone number";
        }
        if (!self::validatePassword($userInfo['password'])) {
            return "Invalid password";
        }
        if ($userInfo['password'] !== $userInfo['confirm-password']) {
            return "Passwords mismatch";
        }
        foreach ($userInfo as $key => $value) {
            if ($key == "confirm-password"  || empty($value)) {
                continue;
            }
            $validMember[$key] = $value;
        }
        return $validMember;
    }
}
