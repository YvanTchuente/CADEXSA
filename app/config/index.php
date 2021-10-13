<?php

define('DOCUMENT_ROOT', dirname(__DIR__));
define('DB_CONFIG_FILE', '/config/db.config.php');

require DOCUMENT_ROOT . '/Classes/Autoload/Loader.php';
require dirname(DOCUMENT_ROOT) . '/vendor/autoload.php';
require DOCUMENT_ROOT . DB_CONFIG_FILE;

use Classes\Error\Handler;
use Classes\Autoload\Loader;
use Classes\Membership\Member;
use Classes\Database\Connection;

session_start();
session_regenerate_id();

Loader::init(DOCUMENT_ROOT . '/');
new Handler(DOCUMENT_ROOT . '/logs'); // Global errors and exceptions handler
$conn = new Connection($params);

$member = new Member($conn);
