<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Bootstrap;

use Cadexsa\Infrastructure\Application;
use Cadexsa\Infrastructure\Config\Repository;

class LoadConfiguration implements Bootstrapper
{
    public function bootstrap(Application $app)
    {
        $app->setConfig($config = new Repository());
        $this->loadConfigurationFiles($app, $config);
    }

    /**
     * Load the configuration items from all of the files.
     * 
     * @throws \LogicException
     */
    private function loadConfigurationFiles(Application $app, Repository $repository)
    {
        $files = $this->getConfigurationFiles($app);
        if (!isset($files['app'])) {
            throw new \LogicException('Unable to load the "app" configuration file.');
        }
        foreach ($files as $name => $file) {
            $repository->set($name, require $file);
        }
    }

    /**
     * Get all of the configuration files for the application.
     */
    private function getConfigurationFiles(Application $app)
    {
        $files = [];
        $configPath = realpath($app->configPath());
        foreach (new \DirectoryIterator($configPath) as $file) {
            if ($file->isDot()) continue;
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }
        ksort($files, SORT_NATURAL);
        return $files;
    }
}
