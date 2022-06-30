<?php

/**
 * Bootstrap file
 * 
 * Loads configuration settings and sets up the environment
 * 
 * @package Cadexsa
 */

require __DIR__ . '/config.php';
require DOCUMENT_ROOT . '/library/Autoload/Loader.php';
require dirname(DOCUMENT_ROOT) . '/vendor/autoload.php';

use Application\Error\Handler;
use Application\Logging\Logger;
use Application\Autoload\Loader;
use Application\PHPMailerAdapter;

session_start();

// Setup class autoloader
Loader::addTranslation('Application', 'library');
Loader::addTranslation("Http\Message", 'Message');
Loader::init(DOCUMENT_ROOT);

// Global exception and error handler
new Handler(DOCUMENT_ROOT . '/logs');

// Global mailer
$mailer = new PHPMailerAdapter(MAILSERVER_HOST, MAILSERVER_ACCOUNTS_ACCOUNT, MAILSERVER_PASSWORD);

// Global logger
$logs_dir = DOCUMENT_ROOT . '/logs';
$logger = new Logger($logs_dir);
$logger->setAlertConfigs(MAILSERVER_ADMIN_ACCOUNT, ADMIN_MAIL, ADMIN_NAME);
$logger->SetMailer($mailer);
