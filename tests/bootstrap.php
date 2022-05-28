<?php
ini_set('memory_limit', '512M');
error_reporting(E_ALL | E_STRICT);

require dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register('loadClass');
function loadClass($className)
{
    $base_dir = dirname(__DIR__);
    $class_dir = (preg_match('/Tests/', $className)) ? "tests\\" : "app\library\\";
    $prefix = (preg_match('/Tests/', $className)) ? "Tests\\" : "Application\\";
    $className = str_replace($prefix, "", $className);
    $filename = str_replace("\\", DIRECTORY_SEPARATOR, $class_dir . $className) . '.php';
    $file = $base_dir . DIRECTORY_SEPARATOR . $filename;
    if (file_exists($file)) {
        require_once $file;
    }
}
