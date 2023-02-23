<?php

declare(strict_types=1);

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\TimeIntervalCalculator;
use Cadexsa\Domain\Model\ExStudent\AuthenticationService;
use Cadexsa\Domain\Model\ExStudent\ExStudentRegistrationService;

class ServiceRegistry
{
    public static function authenticationService()
    {
        return new AuthenticationService;
    }

    public static function memberRegistrationService()
    {
        return new ExStudentRegistrationService;
    }

    public static function timeIntervalCalculator()
    {
        return new TimeIntervalCalculator;
    }
}
