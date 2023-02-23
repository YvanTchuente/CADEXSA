<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Repository\Adapter\PutenvAdapter;

class Environment
{
    /**
     * The environment repository instance.
     */
    protected static ?RepositoryInterface $repository = null;

    /**
     * Get the environment repository instance.
     *
     * @return \Dotenv\Repository\RepositoryInterface
     */
    public static function getRepository()
    {
        if (static::$repository === null) {
            $repository =
                RepositoryBuilder::createWithDefaultAdapters()
                ->addAdapter(PutenvAdapter::class)
                ->immutable()
                ->make();

            static::$repository = $repository;
        }

        return static::$repository;
    }

    /**
     * Gets the value of an environment variable.
     */
    public static function get($key, $default = null)
    {
        return static::getRepository()->get($key) ?? $default;
    }
}
