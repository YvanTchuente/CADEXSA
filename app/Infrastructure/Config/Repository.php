<?php

namespace Cadexsa\Infrastructure\Config;

class Repository implements \ArrayAccess
{
    /**
     * All of the configuration options.
     */
    protected array $options = [];

    /**
     * Create a new configuration repository.
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Determine if the given configuration value exists.
     */
    public function has($key)
    {
        return $this->hasValue($this->options, $key);
    }

    /**
     * Get the specified configuration value.
     */
    public function get(string $key, $default = null)
    {
        return $this->getValue($this->options, $key, $default);
    }

    /**
     * Set a given configuration value.
     */
    public function set(string $key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $this->setValue($this->options, $key, $value);
        }
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key, []);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration options for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->options;
    }

    /**
     * Determine if the given configuration option exists.
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get a configuration option.
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set a configuration option.
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unset a configuration option.
     */
    public function offsetUnset($offset): void
    {
        $this->set($offset, null);
    }

    private function getValue(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        
        return $array;
    }

    private function setValue(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }
            unset($keys[$i]);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
        
        return $array;
    }

    private function hasValue(array $array, $keys)
    {
        $keys = (array) $keys;
        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;
            if (array_key_exists($key, $array)) {
                continue;
            }
            
            foreach (explode('.', $key) as $segment) {
                if (array_key_exists($segment, $subKeyArray)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
