<?php

use Cadexsa\Domain\Model\ExStudent\ExStudent;
use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Infrastructure\Application;
use Cadexsa\Infrastructure\Environment;
use Cadexsa\Presentation\Routing\UrlGenerator;

/**
 * Gets the available application instance.
 */
function app(): Application
{
    return Application::getInstance();
}

/**
 * Get the path to the configuration files.
 */
function config_path(string $path = '')
{
    return app()->configPath($path);
}

/**
 * Get the path to the public directory.
 */
function public_path(string $path = '')
{
    return app()->publicPath($path);
}

/**
 * Get the path to the views directory.
 */
function views_path(string $path = '')
{
    return app()->viewPath($path);
}

/**
 * Get the path to the resources directory.
 */
function resource_path(string $path = '')
{
    return app()->resourcePath($path);
}

/**
 * Get the path to the storage folder.
 *
 * @param  string  $path
 * @return string
 */
function storage_path($path = '')
{
    return app()->storagePath($path);
}

/**
 * Retrieves the value of the specified configuration option.
 */
function config(string $key, $default = null)
{
    if (is_null($key)) {
        return app()->getConfig();
    }
    return app()->getConfig()->get($key, $default);
}

/**
 * Retrieves the value of an environment variable.
 */
function env(string $key, $default = null)
{
    return Environment::get($key, $default);
}

/**
 * Determines if the current user is authenticated.
 */
function isLoggedIn(string $username = null)
{
    return ServiceRegistry::authenticationService()->check($username = null);
}

/**
 * Generates a CSRF token.
 */
function csrf_token()
{
    return bin2hex(random_bytes(16));
}

/**
 * Get the currently authenicated user.
 */
function user(): ExStudent
{
    return $_SESSION['exstudent'];
}

/**
 * Generate the URL to a named route.
 *
 * @param  array|string  $name
 * @param  mixed  $parameters
 * @param  bool  $absolute
 * @return string
 */
function route($name, $parameters = [], $absolute = true)
{
    return (new UrlGenerator(
        app()->getRouter()->getRoutes(),
        app()->currentRequest()
    ))->route($name, $parameters, $absolute);
}
