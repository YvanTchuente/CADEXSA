<?php

define('DOCUMENT_ROOT', dirname(__DIR__));
define('DB_CONFIG_FILE', '/config/db-config-development.php');

require DOCUMENT_ROOT . '/library/Autoload/Loader.php';
require dirname(DOCUMENT_ROOT) . '/vendor/autoload.php';
require DOCUMENT_ROOT . DB_CONFIG_FILE;

use Application\Error\Handler;
use Application\Autoload\Loader;
use Application\Membership\Member;
use Application\Database\Connection;

session_start();
session_regenerate_id();

Loader::init(DOCUMENT_ROOT);
new Handler(DOCUMENT_ROOT . '/logs'); // Global errors and exceptions handler
$conn = new Connection($params);
$member = new Member($conn);
