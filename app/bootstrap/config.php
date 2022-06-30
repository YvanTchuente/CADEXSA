<?php

/**
 * The base configuration for the app
 * 
 * This file contains the following configuration settings:
 * 
 * - Database settings
 * - Mail server settings
 * 
 * @package Cadexsa
 */

/** Document root */
define('DOCUMENT_ROOT', dirname(__DIR__));

/** Administrator settings */

/** Administrator name */
define('ADMIN_NAME', 'Yvan Tchuente');

/** Administrator email address */
define('ADMIN_MAIL', 'yvantchuente@gmail.com');

/** Database settings */

/** The database driver */
define('DB_DRIVER', 'mysql');

/** The name of the database */
define('DB_NAME', 'cadexsa_db');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Mail server configuration settings */

/** Mail server hostname */
define('MAILSERVER_HOST', 'smtp.cadexsa.com');

/** Mail server user password */
define('MAILSERVER_PASSWORD', '20010309');

/** Mail server registered accounts */
define('MAILSERVER_ADMIN_ACCOUNT', 'admin@cadexsa.com');
define('MAILSERVER_ACCOUNTS_ACCOUNT', 'accounts@cadexsa.com');
define('MAILSERVER_NEWSLETTER_ACCOUNT', 'newsletter@cadexsa.com');
define('MAILSERVER_INFO_ACCOUNT', 'info@cadexsa.com');
