<?php

declare(strict_types=1);

namespace Cadexsa\Presentation;

/**
 * Represents a view.
 */
class View
{
    /**
     * The path to the view file.
     */
    private string $path;

    /**
     * The list of view parameters.
     */
    private array $parameters = [];

    public function __construct(string $path, array $parameters = [])
    {
        $this->path = $path;
        $this->parameters = $parameters;
    }

    /**
     * Get the path to the view file.
     */
    public function getPath()
    {
        return $this->name;
    }

    /**
     * Get the list of view parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Adds a parameter to the view.
     */
    public function with(string $name, string $value)
    {
        if (!$name or !$value) {
            throw new \LogicException("Invalid parameter.");
        }
        $this->parameters[$name] = $value;
        
        return $this;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    public function render()
    {
        extract($this->parameters);

        ob_start();
        include_once $this->path; // Loads the view
        $view = ob_get_clean();

        return $view;
    }

    public function __toString()
    {
        return $this->render();
    }
}
