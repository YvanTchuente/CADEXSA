<?php

define('DOCUMENT_ROOT', dirname(__DIR__));

require DOCUMENT_ROOT . '/library/Autoload/Loader.php';
require dirname(DOCUMENT_ROOT) . '/vendor/autoload.php';

use Application\Error\Handler;
use Application\Autoload\Loader;

session_start();
session_regenerate_id();

Loader::init(DOCUMENT_ROOT);
new Handler(DOCUMENT_ROOT . '/logs'); // Global errors and exceptions handler
