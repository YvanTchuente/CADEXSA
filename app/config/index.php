<?php

define('DOCUMENT_ROOT', dirname(__DIR__));

require DOCUMENT_ROOT . '/Classes/Autoload/Loader.php';
require dirname(DOCUMENT_ROOT) . '/vendor/autoload.php';

use Classes\Error\Handler;
use Classes\Autoload\Loader;

Loader::init(DOCUMENT_ROOT . '/');
new Handler(DOCUMENT_ROOT . '/logs'); // Global errors and exceptions handler
