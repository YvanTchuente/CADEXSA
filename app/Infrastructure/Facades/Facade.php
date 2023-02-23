<?php

namespace Cadexsa\Infrastructure\Facades;

use Cadexsa\Infrastructure\Application;

abstract class Facade
{
    /**
     * The application instance being facaded.
     */
    protected static Application $app;

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        $accessor = static::getFacadeAccessor();
        return static::$app->$accessor();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    abstract protected static function getFacadeAccessor();

    /**
     * Get the application instance behind the facade.
     */
    public static function getFacadeApplication()
    {
        return static::$app;
    }

    /**
     * Set the application instance.
     */
    public static function setFacadeApplication(Application $app)
    {
        static::$app = $app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}
