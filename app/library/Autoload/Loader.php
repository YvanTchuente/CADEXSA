<?php

declare(strict_types=1);

namespace Application\Autoload;

/**
 * Class Autoloader
 */
class Loader
{
    private const DIRECTORY_SEPARATOR = "/";

    /**
     * List of directory paths from which to search classes to load
     *
     * @var string[]
     */
    private static $dirs;

    /**
     * @var string[]
     */
    private static $translations = [];

    /**
     * Loads a class
     * 
     * @param string $filename Filename of the class
     * 
     * @return bool
     */
    private static function load(string $filename)
    {
        if (file_exists($filename)) {
            require_once $filename;
            return true;
        }
        return false;
    }

    /**
     * Locates a class and loads it into the current script
     * 
     * @param string $class Classname
     * 
     * @return bool
     */
    public static function autoLoad($class)
    {
        $class = self::getTranslatedClass($class);
        $filename = preg_replace('/\\\/', self::DIRECTORY_SEPARATOR, $class) . '.php';
        foreach (self::$dirs as $dir) {
            $filename = $dir . self::DIRECTORY_SEPARATOR . $filename;
            if (self::load($filename)) {
                return true;
            }
        }
        trigger_error("Unable to load: " . $class);
        return false;
    }

    private static function getTranslatedClass(string $class)
    {
        foreach (self::$translations as $key => $value) {
            if (preg_match("/$key/", $class)) {
                $class = preg_replace("/$key\\\/", "$value\\", $class);
            }
        }
        return $class;
    }

    /**
     * Appends a directory from which to search classes to load
     * 
     * @param string $path Path to the directory
     * 
     * @throws \InvalidArgumentException if `$path` is not a directory
     */
    public static function addDirectory(string $path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("$path is not a directory");
        }
        self::$dirs[] = $path;
    }

    public static function addTranslation(string $name, string $translation)
    {
        if (!$name || !$translation) {
            throw new \LogicException('Some argument(s) is/are empty');
        }
        $name = preg_replace('/\\\/', "\\\\\\", $name);
        $translation = preg_replace('/\\\/', "\\\\\\", $translation);
        self::$translations[$name] = $translation;
    }

    /**
     * Register the loader as an SPL class autoloader
     * 
     * @param string|string[] $dirs Path to a directory or a list of paths 
     *                           to directories from which to search classes
     *                           to load. Defaults to the current directory.
     */
    public static function init($dirs = null)
    {
        if ($dirs) {
            if (is_array($dirs)) {
                foreach ($dirs as $dir) {
                    self::addDirectory($dir);
                }
            } else {
                self::addDirectory($dirs);
            }
        } else {
            self::addDirectory(__DIR__);
        }
        spl_autoload_register(__CLASS__ . '::autoload');
    }
}
