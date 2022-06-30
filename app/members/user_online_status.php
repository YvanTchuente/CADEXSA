<?php

require_once dirname(__DIR__) . '/bootstrap/starter.php';

use Application\Database\Connection;
use Application\MiddleWare\Http\Message\Factory;

$incoming_request = Factory::createServerRequestFromGlobals();

if (isset($incoming_request->getParsedBody()['logout'])) {
    $uid = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    $connection = Connection::Instance()->getConnection();
    $connection->query("DELETE FROM online_members WHERE unique_identifier = '$uid'");
    exit();
}

if (empty($_SESSION)) {
    echo "Not connected";
} else {
    echo "Connected";
}
