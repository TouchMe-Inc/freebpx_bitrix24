<?php

namespace Base;

/**
 * @author TouchMe-Inc
 */
class Config
{
    private $options = [];
    private $keys;

    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    public function getValue($key)
    {
        return $this->options[$key] ?? null;
    }

    public function setValue($key, $value)
    {
        if ($this->isValidKey($key)) {
            $this->options[$key] = $value;
        }
    }

    public function toArray(): array
    {
        return $this->options;
    }

    private function isValidKey($key): bool
    {
        return (bool)array_keys($this->keys, $key);
    }
}