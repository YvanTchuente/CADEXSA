<?php

namespace Application\MiddleWare\Router;

class RouteNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        $message = "Route for the request was not found";
        parent::__construct($message);
    }
}
