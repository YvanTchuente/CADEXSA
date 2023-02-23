<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure;

class PluginManager
{
    /**
     * Loaded plugins.
     */
    private array $plugins = [];

    /**
     * Retrieves the plugin implementation of a given service.
     *
     * @return object
     * 
     * @throws \RuntimeException
     */
    public function getPlugin(string $service)
    {
        if (isset($this->plugins[$service])) {
            return $this->plugins[$service];
        }

        $plugin = env($service);

        if (is_null($plugin)) {
            throw new \RuntimeException("Plugin not specified for $service in configuration settings.");
        }

        try {
            $plugin = new $plugin;
            $this->plugins[$service] = $plugin;

            return $plugin;
        } catch (\Throwable $e) {
            throw new \RuntimeException("The factory is unable to construct an instance of [$service] plugin: {$e->getMessage()}");
        }
    }
}
