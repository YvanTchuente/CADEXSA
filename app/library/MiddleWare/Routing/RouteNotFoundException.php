<?php

namespace Application\MiddleWare\Routing;

class RouteNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        $message = "Route for the request was not found";
        parent::__construct($message);
    }
}
